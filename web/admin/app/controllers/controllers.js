;(function (angular) {
    "use strict";

    angular.module('tasks.admin.controllers', [
        'tasks.admin.controller.dashboard',
        'tasks.admin.controller.tasks'
        // 'tasks.admin.controller.security',
        // 'tasks.admin.controller.profile'
    ]);
})(window.angular);