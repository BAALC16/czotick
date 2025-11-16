/******/ (() => { // webpackBootstrap
    var __webpack_exports__ = {};
    /*!***************************************************!*\
      !*** ./resources/js/pages/project-create.init.js ***!
      \***************************************************/

// ckeditor
    ClassicEditor.create(document.querySelector('#description')).then(function (editor) {
        editor.ui.view.editable.element.style.height = '200px';
    })["catch"](function (error) {
        console.error(error);
    });

    if ($('#nearby').length) {

        ClassicEditor.create(document.querySelector('#nearby')).then(function (editor) {
            editor.ui.view.editable.element.style.height = '200px';
        })["catch"](function (error) {
            console.error(error);
        }); // Dropzone
    }

// FilePond
    FilePond.registerPlugin(
        // encodes the file as base64 data
        FilePondPluginFileEncode,
        // validates the size of the file
        FilePondPluginFileValidateSize,
        // corrects mobile image orientation
        FilePondPluginImageExifOrientation,
        // previews dropped images
        FilePondPluginImagePreview,
        FilePondPluginFileValidateType
    );

    var activityImage = document.getElementById('activity');

    FilePond.create(activityImage,
        {
            maxFiles: 10,
            allowMultiple: true,
            acceptedFileTypes: ['image/png', 'image/jpg', 'image/jpeg'],
            files: [activity],
            fileValidateTypeDetectType: (source, type) =>
                new Promise((resolve, reject) => {
                    // Do custom type detection here and return with promise

                    resolve(type);
                }),
        }

    );

    /******/ })()
;
"use strict";
jQuery(function() {
    $('#repeater').repeater({
        initEmpty: false,
        show: function() {
            $(this).slideDown();
        },

        hide: function(deleteElement) {
            $(this).slideUp(deleteElement);
        },

        ready: function() {
            console.log("Repeater loaded.");
        }
    });

})
