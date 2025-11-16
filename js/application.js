jQuery(function () {
  // With button spinner
  $(".form-xhr").submit(function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    let $wrapper = $(this).closest('.blockui')
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
            confirmButton: "btn font-weight-bold btn-danger",
          },
        });
      },
      success: function (data) {
        $wrapper.waitMe('hide')
        if (data.success) {
          f[0].reset();
          if(data.message) {
            swal.fire({
              html: typeof data.message != "undefined" && data.message.length ? data.message : "Terminé avec succès !",
              icon: "success",
              buttonsStyling: false,
              confirmButtonText: "Continuer",
              customClass: {
                confirmButton: "btn font-weight-bold btn-primary",
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
          } else {
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
          }
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
              confirmButton: "btn font-weight-bold btn-danger",
            },
          });
        }
      },
    });
  });

  // if($('.dropify').length) {
  //   $('.dropify').dropify({...dropify_config})
  // }

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
          _method: 'DELETE',
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

  $('.toggle-password-visibility').click(function(e){
    $(this).toggleClass('fa-eye-slash')
    $(this).toggleClass('fa-eye')
    let pwd_field = $(this).closest('.form-group')[0].querySelector('input[name="password"]')
    $(pwd_field).attr('type', $(pwd_field).attr('type') == 'password' ? 'text' : 'password')
  })


  $(document).on('change', '.custom-file-input', function(e) {
    let field = e.target, _parent = $(field).closest('.custom-file')[0], label = _parent.querySelector('label');
    if(label) {
      $(label).text(field.files[0].name)
    }
  })

  $('.select-submit').on('change', function() {
    var $form = $(this).closest('form');
    $form.find('button[type=submit]').click();
  });

  $(document).on('click',".wishlist",function(e){
      e.preventDefault();
      var id=$(this).attr('data-id');
      var type=$(this).attr('data-type');
      console.log(id);
      console.log('type',type);
      if ($(this).hasClass('wishlist-added')) {
          $(this).removeClass('wishlist-added');
          $(this).children().eq(0).removeClass('fas')
          $(this).children().eq(0).addClass('far')
      }else {
          $(this).addClass('wishlist-added');
          $(this).children().eq(0).removeClass('far')
          $(this).children().eq(0).addClass('fas');

      }

      $(this).tooltip('hide');
      $.ajax({
          url:'/add-wishlist',
          type:"GET",
          data:{id:id,type:type},
          success:function(response){

                console.log(response);
          },
          error:function (error){

          }
      })
  })

});
