;(function (angular) {
    "use strict";

    var taskModule = angular.module('tasks.admin.controller.tasks', ['ui.router']);

    taskModule.config(['$stateProvider', function ($stateProvider) {
        $stateProvider
            .state('task', {
                url: '/task',
                templateUrl: '/admin/views/task/main.html',
                pageTitle: 'Task'
            })
            .state('task.list', {
                url: '/list',
                templateUrl: '/admin/views/task/search.html',
                pageTitle: 'List',
                controller: [
                    '$scope',
                    '$state',
                    '$AdminRequest',
                    '$uibModal',
                    'Notification',
                    TaskListController
                ]
            })
            .state('task.create', {
                url: '/create',
                templateUrl: '/admin/views/task/create.html',
                pageTitle: 'Create',
                controller: [
                    '$scope',
                    '$state',
                    'Notification',
                    '$AdminRequest',
                    TaskCreateController
                ]
            })
            .state('task.edit', {
                url: '/{task}/edit',
                templateUrl: '/admin/views/task/edit.html',
                pageTitle: 'Edit',
                controller: [
                    '$scope',
                    '$AdminRequest',
                    '$stateParams',
                    '$state',
                    'Notification',

                    TaskEditController
                ]
            });
    }]);

    taskModule.directive('taskForm', function () {
        return {
            restrict: 'E',
            replace: true,
            templateUrl: '/admin/views/task/form.html'
        };
    });

    function TaskListController ($scope, $state, $AdminRequest, $uibModal, Notification)
    {
        var loadList = function() {
            var params = {
                page: 1,
                limit: 10
            };
            $AdminRequest.request('/manage/task/list', 'POST', params).then(
                function(response) {
                    $scope.pagination = response.data;
                },
                function(response) {
                    console.log(response);
                }
            );
        };

        loadList();
console.log($scope)
        $scope.openModalForRemove = function (task)
        {
            $uibModal.open({
                templateUrl: '/admin/views/task/remove.html',
                controller: TaskRemoveController(task, function () {

                    Notification.success({
                        message: 'Successfully remove task.'
                    });
                    $state.reload();
                })
            })
        };
    }

    function TaskCreateController ($scope, $state, Notification, $AdminRequest)
    {
        $scope.task = Task.fromNull();

        $scope.create = factoryForSaveProduct(
            $scope,
            $state,
            Notification,
            $AdminRequest,
            '/manage/task/create',
            'Successfully create task.'
        );

    }

    function factoryForSaveProduct ($scope, $state, Notification, $AdminRequest, url, message)
    {
        return function () {
            $scope.task.errors = null;
            console.log($scope);
            $scope.task.processed = true;
            var params = {
                id: $scope.task.id,
                name: $scope.task.name,
                description: $scope.task.description,
                startedAt: $scope.task.startedAt ? new Date($scope.task.startedAt).toDateString() : null,
                finishedAt: $scope.task.finishedAt ? new Date($scope.task.finishedAt).toDateString() : null,
                status: $scope.task.status
            };

            $AdminRequest
                .request(url, 'POST', params)
                .then(
                    function () {
                        $state.go('task.list')
                            .then(function () {
                                Notification.success(message);
                            });
                    },

                    function (response) {
                        console.log('error', response);
                            $scope.task.errors = response.data.error.data;
                            $scope.task.processed = false;
                    }
                );
        };
    }


    function TaskEditController ($scope, $AdminRequest, $stateParams, $state, Notification)
    {
        var
            taskId = $stateParams.task,

            loadTask = function ()
            {
                $AdminRequest.request('/manage/task', 'POST', {id: taskId})
                    .then(
                        function (response) {
                            $scope.task = Task.fromApiResponse(response.data.data.task);
                            console.log(response, $scope)
                        }
                    )
            };

        $scope.save = factoryForSaveProduct(
            $scope,
            $state,
            Notification,
            $AdminRequest,
            '/manage/task/edit',
            'Successfully update product.'
        );
        loadTask();
    }

    function TaskRemoveController (task, callback)
    {
        return [
            '$scope',
            '$uibModalInstance',
            '$AdminRequest',

            function ($scope, $uibModalInstance, $AdminRequest) {
                $scope.task = task;
                $scope.task.processed = false;

                $scope.cancel = function () {
                    $uibModalInstance.close();
                };

                $scope.remove = function () {
                    $scope.task.processed = true;
                    $AdminRequest.request('/manage/task/remove', 'POST', {id: task.id})
                        .then(
                            function (response) {
                                $scope.task.processed = false;
                                $uibModalInstance.close();
                                callback();
                            },

                            function () {
                                $scope.task.processed = false;
                            }
                        );
                }
            }
        ];
    }
})(window.angular);