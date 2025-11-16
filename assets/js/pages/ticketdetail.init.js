/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};


var selector = document.getElementById("prestataire_id");
const choices = new Choices(selector);

choices.setChoices(
  async () => {
    try {
      const dataSource = selector.getAttribute("data-url");
      // Fetch Data from external Source
      const source = await fetch(dataSource);
      // Data is array of `Objects` | `Strings`
      const data = await source.json();
      
      let arr = [];
      for (let i = 0; i < data.length; i++) {
          let element = data[i];
          arr.push({ "value": element.id, "label": element.prenoms + ' ' + element.nom});
      }
      return arr;
    } catch (error) {
      return error;
    }
  }
 
);

jQuery(function() {

  function doPost(elt) {
    $('#layout-wrapper').waitMe({
    });
    $.post(elt.data('href'), {
            _token: $("meta[name=csrf-token]").attr("content"),
            _method: elt.attr('data-method'),
        })
        .done((r) => {
            $('#layout-wrapper').waitMe('hide');
            Swal.fire({
                text: r.message,
                icon: "success",
                buttonsStyling: false,
                confirmButtonText: "D'accord",
                customClass: {
                    confirmButton: "btn font-weight-bold btn-primary",
                },
            }).then(function() {
                if (typeof r.redirect != "undefined")
                    document.location.href = r.redirect;
            });
        })
        .fail((er) => {
            $('#layout-wrapper').waitMe('hide');
            Swal.fire({
                text: er.responseJSON.message,
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "D'accord",
                customClass: {
                    confirmButton: "btn font-weight-bold btn-danger",
                },
            });
        });
    }
    $('.switcher').click(function() {
            var Elt = $(this)
            if ($(this).hasClass('confirm')) {
                Swal.fire({
                    text: $(this).data('confirm'),
                    icon: "question",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Oui, je confirme",
                    cancelButtonText: "Non, annuler",
                    customClass: {
                        confirmButton: "btn font-weight-bold btn-danger",
                        cancelButton: "btn font-weight-bold btn-active-light-primary",
                    },
                }).then(function(result) {
                    if (result.value) {
                        doPost(Elt);
                    }
                });
            } else {
                doPost(Elt);
        }
    });

    $('#comment-files').change(function(e){
        let files_count = e.target.files.length
        $('#btn-comment-files').text('Joindre des fichiers '+(files_count > 1 ? '('+files_count+' fichiers)' : '(1 fichier)'))
    });

    $('#message-bottom').focus();
});
// favourite btn
document.querySelectorAll(".favourite-btn").forEach(function (item) {
  item.addEventListener("click", function (event) {
    this.classList.toggle("active");
  });
});

var lightbox=GLightbox({selector:".image-popup",title:!1});
/******/ })()
;