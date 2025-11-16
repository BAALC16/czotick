/******/ (() => { // webpackBootstrap
    var __webpack_exports__ = {};
    /*!***************************************************!*\
      !*** ./resources/js/pages/project-create.init.js ***!
      \***************************************************/

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

    var officeImage = document.getElementById('office');

    FilePond.create(officeImage,
        {
            maxFiles: 10,
            allowMultiple: true,
            acceptedFileTypes: ['image/png', 'image/jpg', 'image/jpeg'],
            files: [office],
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
