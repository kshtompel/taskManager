;(function (window) {
    "use strict";

    /**
     * Task model
     *
     * @param {String}                     id
     * @param {String}                     name
     * @param {String}                     description
     * @param {Date}                       createdAt
     * @param {Date}                       startedAt
     * @param {Date}                       finishedAt
     * @param {Number}                     status
     * @constructor
     */
    function Task (
        id,
        name,
        description,
        createdAt,
        startedAt,
        finishedAt,
        status
    )
    {
        this.id = id;
        this.name = name;
        this.description = description;
        this.createdAt = createdAt ? new Date(createdAt) : null;
        this.startedAt = startedAt ? new Date(startedAt) : null;
        this.finishedAt = finishedAt ? new Date(finishedAt) :  null;
        this.status = status;
    }

    /**
     * Create a new product from API response
     *
     * @param {Object} info
     *
     * @returns {Task}
     */
    Task.fromApiResponse = function (info)
    {
        var translations = [];

        return new Task(
            info.id,
            info.name,
            info.description,
            new Date(info.createdAt),
            new Date(info.startedAt),
            new Date(info.finishedAt),
            info.status
        );
    };

    Task.fromNull = function ()
    {
        return new Task(
            null,
            null,
            null,
            null,
            null,
            null,
            1
        );
    };

    window.Task = Task;
})(window);