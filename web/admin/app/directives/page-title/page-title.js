;(function (angular) {
    "use strict";

    angular.module('TaskManager')
        .directive('title', ['$rootScope', '$timeout', '$state', function($rootScope, $timeout, $state) {
            return {
                link: function () {
                    $rootScope.$on('$stateChangeSuccess', function() {
                        $timeout(function () {
                            var titles = ["Task Manager"],
                                stateName, state;

                            for (stateName in $state.$current.includes) {
                                if ($state.$current.includes.hasOwnProperty(stateName)) {
                                    if (stateName) {
                                        state = $state.get(stateName);
                                        if (state && state.hasOwnProperty('pageTitle') && state.pageTitle) {
                                            titles.push(state.pageTitle);
                                        }
                                    }
                                }
                            }

                            titles.reverse();

                            $rootScope.title = titles.length > 0 ? titles.join(' :: ') : 'Task Manager';
                        });
                    })
                }
            };
        }]);
})(window.angular);