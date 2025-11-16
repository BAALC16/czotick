jQuery(function () {
  // With button spinner
  $(".form-xhr").submit(function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    let $wrapper = $(this).closest('div')
    $wrapper.waitMe({...waitMe_config})
    let f = $(this),
      dat = new FormData(f[0]);
    $.ajax({
      url: f.attr("action"),
      type: f.attr("method"),
      dataType: "JSON",
      data: dat,
      processData: false,
      contentType: false,
      error: function (error) {
        $wrapper.waitMe('hide')
        let txt = "";
        if (error.status == 422) {
          txt += "<div class='text-start'>";
          for (let m in error.responseJSON.errors) {
            for (let n in error.responseJSON.errors[m]) {
              txt += "- " + error.responseJSON.errors[m][n] + "<br>";
            }
          }
          txt += "</div>";
        } else {
          txt = error.responseJSON.message;
        }
        swal.fire({
          html: txt,
          icon: "error",
          buttonsStyling: false,
          confirmButtonText: "D'accord",
          customClass: {
            confirmButton: "btn fw-bold btn-danger",
          },
        });
      },
      success: function (data) {
        $wrapper.waitMe('hide')
        if (data.success) {
          f[0].reset();
          swal.fire({
              html: typeof data.message != "undefined" && data.message.length ? data.message : "Terminé avec succès !",
              icon: "success",
              buttonsStyling: false,
              confirmButtonText: "Continuer",
              timer: 5000,
              customClass: {
                confirmButton: "btn fw-bold btn-primary",
              },
            })
            .then(function () {
              if (typeof data.redirect != "undefined")
                document.location.href = data.redirect;
              else if (typeof data.event != "undefined") {
                console.log(
                  "Triggering " + data.event + " event ..."
                );
                $.event.trigger({
                  type: data.event,
                  parameters: data.parameters,
                });
              } else {
                console.log("No triggerable event.");
              }
            });
        } else if (typeof data.event != "undefined") {
          console.log("Triggering " + data.event + " event ...");
          $.event.trigger({
            type: data.event,
            parameters: data.parameters,
          });
        } else {
          swal.fire({
            html: data.message,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "D'accord",
            customClass: {
              confirmButton: "btn fw-bold btn-danger",
            },
          });
        }
      },
    });
  });


  $(document).on('change', '.custom-file-input', function(e) {
    let field = e.target, _parent = $(field).closest('.custom-file')[0], label = _parent.querySelector('label');
    if(label) {
      $(label).text(field.files[0].name)
    }
  })

  /*$.fn.blockAjax = function(_a) {
    var f = $('.hidden-form'), dat = new FormData(f[0]);
    f.attr('action', _a.data('href'));
    var btn;
    if(_a.hasClass('btn-spinner')) {
      btn = KTUtil.getById(_a.attr('id'));
      KTUtil.btnWait(btn, "spinner spinner-right spinner-white pr-15", "Chargement");
    } else {
      btn = false;
    }
    KTApp.blockPage({
      overlayColor: '#000000',
      state: 'info',
      message: 'Veuillez patienter...'
    });
    $.ajax({
      url: f.attr('action'),
      type: "POST",
      dataType: 'JSON',
      data: dat,
      processData: false,
      contentType: false,
      error: function(error) {
        KTApp.unblockPage();
        let txt = "";
        if(error.status == 422) {
          txt += "<div class='text-left'>"
          for(let m in error.responseJSON.errors) {
            for (let n in error.responseJSON.errors[m]) {
              txt += "- " + error.responseJSON.errors[m][n] + "<br>";
            }
          }
          txt += "</div>"
        } else {
          txt = error.responseJSON.message;
        }
        swal.fire({
          html: txt,
          icon: "error",
          buttonsStyling: false,
          confirmButtonText: "D'accord",
          customClass: {
            confirmButton: "btn font-weight-bold btn-light-primary"
          }
        }).then(function() {
          // KTUtil.scrollTop();
          if(btn) KTUtil.btnRelease(btn);
          console.log("Form submission failed.");
        });
      },
      success: function(data) {
        KTApp.unblockPage();
        if(btn) KTUtil.btnRelease(btn);
        if(data.message) {
          swal.fire({
            html: data.message,
            icon: "success",
            buttonsStyling: false,
            confirmButtonText: "Continuer",
            customClass: {
              confirmButton: "btn font-weight-bold btn-light-primary"
            }
          }).then(function() {
            if(data.next) {
              if(data.next == 'rm-row') {
                _a.closest('tr').fadeOut('slow', function(el) {
                  _a.closest('tr').remove()
                })
              }
            }
            if(data.redirect)
              document.location.href = data.redirect
            if (typeof(data.event_to_trigger) != 'undefined') {
              console.log("Triggering "+data.event_to_trigger+" event ...");
              $.event.trigger({
                type: data.event_to_trigger,
                parameters: data.parameters
              })
            } else {
              console.log("No triggerable event.");
              return true;
            }
          });
        } else {
          if(data.next) {
            if(data.next == 'rm-row') {
              _a.closest('tr').fadeOut('slow', function(el) {
                _a.closest('tr').remove()
              })
            }
          }
          if(data.redirect)
            document.location.href = data.redirect
          if (typeof(data.event_to_trigger) != 'undefined') {
            console.log("Triggering "+data.event_to_trigger+" event ...");
            $.event.trigger({
              type: data.event_to_trigger,
              parameters: data.parameters
            })
          }
        }
      }
    }); // end ajax()
  }

  $.fn.staticPost = function() {
    var a = $(this);
    if(a.hasClass('confirm')) {
      Swal.fire({
        title: "Attention !",
        text: a.data('confirm'),
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Oui, continuer",
        cancelButtonText: "Annuler"
      }).then(function(result) {
        if (result.value) {
          a.blockAjax(a);
        }
      });
    } else {
      a.blockAjax(a);
    }
  }; */

  // Pickers
  /*$.fn.datepicker.dates['fr'] = {
    days: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
    daysShort: ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"],
    daysMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
    months: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
    monthsShort: ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Aoû", "Sep", "Oct", "Nov", "Déc"],
    today: "Aujourd'hui",
    clear: "Effacer",
    format: "yyyy-mm-dd",
    titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
  // weekStart: 1
  //   };
  // });

  if($('.dropify').length) {
    $('.dropify').dropify({...dropify_config})
  }

  $(document).on('click', '.click-to-delete-row', function(e) {
    e.preventDefault();
    // Select parent row
    let parent = $(this).closest('tr');
    let elt = $(this);

    Swal.fire({
      text: elt.data('confirm'),
      icon: "question",
      showCancelButton: true,
      buttonsStyling: false,
      confirmButtonText: "Oui, je confirme",
      cancelButtonText: "Non, annuler",
      customClass: {
        confirmButton: "btn fw-bold btn-danger",
        cancelButton: "btn fw-bold btn-active-light-primary",
      },
    }).then(function (result) {
      if (result.value) {
        $('#content').waitMe({...waitMe_config});
        $.post(elt.data('href'), {
          _token: $("meta[name=csrf-token]").attr("content"),
          _method: 'GET',
        })
        .done((r) => {
          $('#content').waitMe('hide');
          Swal.fire({
            text: r.message,
            icon: "success",
            buttonsStyling: false,
            confirmButtonText: "D'accord",
            customClass: {
              confirmButton: "btn fw-bold btn-primary",
            },
          }).then(function () {
            parent.fadeOut('slow', function(el) {
              parent.remove()
            })
          });
        })
        .fail((er) => {
          $('#content').waitMe('hide');
          Swal.fire({
            text: er.responseJSON.message,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "D'accord",
            customClass: {
              confirmButton: "btn fw-bold btn-danger",
            },
          });
        });
      }
    });
  })

});
