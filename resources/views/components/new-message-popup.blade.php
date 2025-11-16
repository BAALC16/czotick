@props(['user'])

<div class="modal fade" id="modal-messenger" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header border-0 pb-0">
              <h4 class="modal-title text-heading" id="exampleModalLabel">Contact Form</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body pb-6">
              <div class="form-group mb-2">
                  <input type="text" class="form-control form-control-lg border-0"
                      placeholder="{{$user->full_name}}">
              </div>
              <div class="form-group mb-2">
                  <input type="email" class="form-control form-control-lg border-0" placeholder="Your Email">
              </div>
              <div class="form-group mb-2">
                  <input type="tel" class="form-control form-control-lg border-0" placeholder="Your phone">
              </div>
              <div class="form-group mb-2">
                  <textarea class="form-control border-0"
                      rows="4">Hello, I'm interested in Villa Called Archangel</textarea>
              </div>
              <div class="form-group form-check mb-4">
                  <input type="checkbox" class="form-check-input" id="exampleCheck3">
                  <label class="form-check-label fs-13" for="exampleCheck3">Egestas fringilla phasellus faucibus
                      scelerisque eleifend donec.</label>
              </div>
              <button type="submit" class="btn btn-primary btn-lg btn-block rounded">Request Info</button>
          </div>
      </div>
  </div>
</div>