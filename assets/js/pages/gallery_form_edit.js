/******/ (() => { // webpackBootstrap
    var __webpack_exports__ = {};
    /*!***************************************************!*\
      !*** ./resources/js/pages/project-create.init.js ***!
      \***************************************************/


FilePond.registerPlugin(
    // encodes the file as base64 data
    FilePondPluginFileEncode,
    // validates the size of the file
    FilePondPluginFileValidateSize,
    // corrects mobile image orientation
    FilePondPluginImageExifOrientation,
    // previews dropped images
    FilePondPluginImagePreview,

    // crops the image to a certain aspect ratio
    FilePondPluginImageCrop,
  
    // applies crop and resize information on the client
    FilePondPluginImageTransform,

    FilePondPluginImageResize,
  
);

    var inputElement = document.getElementById('gallery');

    // create a FilePond instance at the input element location
    FilePond.create(inputElement,
    {
        maxFiles: 20,
        allowMultiple: true,
        acceptedFileTypes: ['image/png', 'image/jpg', 'image/jpeg'],
        files: images,
        imageCropAspectRatio: '1:1',
        imageResizeTargetWidth: 800,
        imageResizeTargetHeight: 533,
        imageResizeMode: 'cover',
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
