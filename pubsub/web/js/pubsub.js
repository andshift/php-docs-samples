
'use strict';

var pubsub = pubsub || angular.module('pubsub', []);

/**
 * PubsubController.
 *
 * @NgInject
 */
pubsub.PubsubController = function($http, $log, $timeout) {
  this.promise = null;
  this.logger = $log;
  this.http = $http;
  this.timeout = $timeout;
  this.interval = 1;
  this.isAutoUpdating = true;
  this.failCount = 0;
  this.hasAttemptedToCreateTopicAndSubscription = false;
  this.messages = [];
  this.fetchMessages();
};

pubsub.PubsubController.MAX_FAILURE_COUNT = 3;

pubsub.PubsubController.TIMEOUT_MULTIPLIER = 1000;

/**
 * Toggles the auto update flag.
 */
pubsub.PubsubController.prototype.toggleAutoUpdate = function() {
  this.isAutoUpdating = !this.isAutoUpdating;
  if (this.isAutoUpdating) {
    this.logger.info('Start fetching.');
    this.fetchMessages();
  } else if (this.promise !== null) {
    this.logger.info('Cancel the promise.');
    this.timeout.cancel(this.promise);
    this.promise = null;
  }
};

/**
 * Sends a message
 *
 * @param {string} message
 */
pubsub.PubsubController.prototype.sendMessage = function(message) {
  var self = this;
  self.http({
    method: 'POST',
    url: '/send_message',
    data: 'message=' + encodeURIComponent(message),
    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
  }).success(function(data, status) {
    self.message = null;
  }).error(function(data, status) {
    self.logger.error('Failed to send the message. Status: ' + status + '.');
    if (!self.hasAttemptedToCreateTopicAndSubscription) {
      // Try to create the topic and subscription once.
      self.hasAttemptedToCreateTopicAndSubscription = true;
      self.logger.info('Trying to create the topic and subscription...');
      self.http({
        method: 'POST',
        url: '/create_topic_and_subscription'
      }).success(function(data, status) {
        // Try one more time to send the message.
        self.sendMessage(message);
      }).error(function(data, status) {
        self.logger.error('Failed to create the topic and subscription. ' +
            'Status: ' + status + '.');
      });
    }
  });
};

/**
 * Continuously fetches messages from the server.
 */
pubsub.PubsubController.prototype.fetchMessages = function() {
  var self = this;
  self.http.get('/fetch_messages')
    .success(function(data, status) {
      self.messages.push.apply(self.messages, data);
      self.failCount = 0;
    })
    .error(function(data, status) {
      self.logger.error('Failed to receive the messages. Status: ' +
                        status + '.');
      self.failCount += 1;
    });
  if (self.failCount < pubsub.PubsubController.MAX_FAILURE_COUNT) {
    if (self.isAutoUpdating) {
      self.promise = self.timeout(
        function() { self.fetchMessages(); },
        self.interval * pubsub.PubsubController.TIMEOUT_MULTIPLIER);
    }
  } else {
    self.errorNotice = 'Maximum failure count reached, ' +
      'so stopped fetching messages.';
    self.logger.error(self.errorNotice);
    self.isAutoUpdating = false;
    self.failCount = 0;
  }
};
