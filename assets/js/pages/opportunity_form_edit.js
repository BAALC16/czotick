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

    var opportunityImage = document.getElementById('opportunity');

    FilePond.create(opportunityImage,
        {
            maxFiles: 10,
            allowMultiple: true,
            acceptedFileTypes: ['image/png', 'image/jpg', 'image/jpeg'],
            files: [opportunity],
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

    $('#purpose_radio').change(function(){
        var val = $("input[name='purpose']:checked").val();

        if (val == "vente") {
            $("#price_label").text("Prix");
            $("#price_label1").text("FCFA");

            $("input[name='guarantee']").val("");
            $("#guarantee_block").hide();
            $("input[name='key_money']").val("");
            $("#key_money_block").hide();

        }
        else {
            $("#price_label").text("Loyer");
            $("#price_label1").text("FCFA / mois");
            $("#guarantee_block").show();
            $("#key_money_block").show();
        }
    });
})
