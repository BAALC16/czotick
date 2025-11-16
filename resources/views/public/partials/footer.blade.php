<footer id="footer" class="footer" data-tm-bg-img="images/footer-bg.png">
    <div class="footer-widget-area">
      <div class="container pt-90 pb-40">
        <div class="row">
          <div class="col-md-6 col-lg-6 col-xl-4">
            <div class="widget tm-widget-contact-info contact-info-style1 contact-icon-theme-colored1">
              <div class="thumb">
                <img alt="Logo" src="{{ URL::asset('assets/front/images/logo1.png') }}">
              </div>
              <div class="description">La JCI Nirvana est une organisation internationale à but non lucratif composée de jeunes professionnels, entrepreneurs et citoyens engagés. Nous croyons au pouvoir de l'action positive pour créer un changement durable.</div>
              <ul class="mb-30">
                <li class="contact-phone">
                  <div class="icon"><i class="flaticon-contact-042-phone-1"></i></div>
                  <div class="text"><a href="javascript:void(0)">+2250708903709</a></div>
                </li>
                <li class="contact-email">
                  <div class="icon"><i class="flaticon-contact-043-email-1"></i></div>
                  <div class="text"><a href="javascript:void(0)">info@nirvana.ci</a></div>
                </li>
                <!-- <li class="contact-website">
                  <div class="icon"><i class="flaticon-contact-035-website"></i></div>
                  <div class="text"><a target="_blank" href="http://yourdomain.com">www.yourdomain.com</a></div>
                </li> -->
              </ul>
            </div>
          </div>
          <div class="col-md-6 col-lg-6 col-xl-2">
            <div class="widget widget_nav_menu">
              <h4 class="widget-title line-bottom">Liens utiles</h4>
              <ul>
                <li><a href="/presentation">Présentation</a></li>
                <li><a href="https://www.facebook.com/jcicotedivoire225" target="_blank">JCI CI</a></li>
                <li><a href="https://www.facebook.com/jciafme" target="_blank">JCI AFME</a></li>
                <li><a href="https://www.linkedin.com/company/junior-chamber-international/" target="_blank">JCI</a></li>
                <li><a href="https://jci.cc/" target="_blank">JCI CC</a></li>
              </ul>
            </div>
          </div>
        <!--  <div class="col-md-6 col-lg-6 col-xl-3">
            <div class="widget">
              <h4 class="widget-title line-bottom">Twitter Feed</h4>
              <div class="twitter-feed list-border clearfix" data-username="Envato" data-count="2"></div>
            </div>
          </div> -->
          <div class="col-md-6 col-lg-6 col-xl-3">
            <div class="widget">
              <h4 class="widget-title line-bottom">Tenue des reunions</h4>
              <div class="opening-hours border-dark">
                <ul>
                  <li class="clearfix"> <span> Mercredi :  18H30</span>
                    <!-- <div class="value"> 18H30 </div> -->
                  </li>
                <!--  <li class="clearfix"> <span> Wednes - Thurs :</span>
                    <div class="value"> 8.00 am - 6.00 pm </div>
                  </li>
                  <li class="clearfix"> <span> Fri :</span>
                    <div class="value"> 3.00 pm - 8.00 pm </div>
                  </li>
                  <li class="clearfix"> <span> Sun : </span>
                    <div class="value"> Closed </div>
                  </li> -->
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-5 col-lg-5">
            <div class="widget">
                <h5 class="widget-title mb-0">S'abonner à notre newsletter</h5>
                <!-- Mailchimp Subscription Form-->
                <form id="mailchimp-subscription-form1" class="newsletter-form lh-1">
                    @csrf
                    <label for="mce-EMAIL"></label>
                    <div class="input-group subscribe-form">
                        <input type="email" name="email" id="email" data-tm-height="45px" class="form-control" placeholder="Votre email">
                        <div class="input-group-append tm-sc-button">
                        <button type="submit" id="newsletter" class="btn btn-theme-colored1 btn-sm" data-tm-height="45px">Valider</button>
                    </div>
                    </div>
                </form>
              <!-- Mailchimp Subscription Form Validation-->
             
            </div>
          </div>
          <!-- <div class="col-sm-6 col-md-3 col-lg-3">
            <div class="widget text-center text-xl-start">
              <h5 class="widget-title mb-10">Call Us Now</h5>
              <div class="text-gray">
                +61-3-1234-5678 <br>
                +12-3-1234-5678
              </div>
            </div>
          </div> -->
          <div class="col-sm-6 col-md-4 col-lg-4">
            <div class="widget text-center text-xl-start">
              <h5 class="widget-title mb-10">Suivez-nous</h5>
              <ul class="styled-icons icon-dark icon-theme-colored1 icon-rounded clearfix">
                <li><a class="social-link" href="https://www.linkedin.com/company/jci-nirvana/" target="_blank"><i class="fab fa-linkedin"></i></a></li>
                <li><a class="social-link" href="https://www.facebook.com/jcinirvana" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                <li><a class="social-link" href="https://www.instagram.com/jcinirvana" target="_blank"><i class="fab fa-instagram"></i></a></li>
                <li><a class="social-link" href="https://www.tiktok.com/@jcinirvana" target="_blank"><i class="fab fa-tiktok"></i></a></li>
                <!-- <li><a class="social-link" href="#" ><i class="fab fa-skype"></i></a></li>
                <li><a class="social-link" href="#" ><i class="fab fa-youtube-square"></i></a></li> -->
                <!-- <li><a class="social-link" href="#" ><i class="fab fa-pinterest"></i></a></li> -->
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="footer-bottom" data-tm-bg-color="#2A2A2A">
        <div class="container">
          <div class="row pt-20 pb-20">
            <div class="col-sm-6">
              <div class="footer-paragraph">
                Copyright ©2024 Nirvana. Tous droits réservés.
              </div>
            </div>
            {{-- <div class="col-sm-6">
              <div class="footer-paragraph text-end">
                <div class="widget-pages-link font-size-14">
                  <a class="mr-10" href="#">FAQ</a>
                  <a class="mr-10" href="#">|</a>
                  <a class="mr-10" href="#">Help Desk</a>
                  <a class="mr-10" href="#">|</a>
                  <a href="#">Support</a>
                </div>
              </div>
            </div> --}}
          </div>
        </div>
      </div>
    </div>
  </footer>
  <a class="scrollToTop" href="#"><i class="fa fa-angle-up"></i></a>