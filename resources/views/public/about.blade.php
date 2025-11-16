@extends('public.layouts_index')
@section('content')
<section class="page-title divider layer-overlay overlay-dark-5 section-typo-light bg-img-center" data-tm-bg-img="{{ URL::asset('assets/front/images/ban.png') }}">
    <div class="container pt-90 pb-90">
      <!-- Section Content -->
      <div class="section-content">
        <div class="row">
          <div class="col-md-12 text-center">
            <h2 class="title text-white">Présentation</h2>
            {{ Breadcrumbs::render('presentation') }} 
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Section: inner-header End -->

  <!-- Section: Présentation -->
  <section>
    <div class="container pb-30 pt-60">
      <div class="section-content">
        <div class="row">
          <div class="col-sm-8 col-lg-8">
            <div class="Présentation-content mb-30">
              <div class="icon-text">
                <h2 class="text-upppercase">Le mot de bienvenue du Président Exécutif 2025</h2>
                {{-- <p class="lead mb-15">Your support will help us to make life better living for poor children.</p> --}}
                <p class="mb-15" style="text-align: justify;">Chers membres émérites, distingués partenaires, illustres visiteurs,
                  <br>C’est avec gratitude et respect que je vous accueille sous l’égide de la mandature 2025 de la Jeune Chambre Internationale Nirvana. Cette année, portée par les couleurs du jaune moutarde et du vert forêt, incarne une ambition renouvelée : bâtir des ponts entre engagement citoyen, excellence professionnelle et impact durable.
                  <br>Votre présence, qu’elle soit active, stratégique ou bienveillante, témoigne de votre foi en notre capacité collective à transcender les défis de notre époque et à inspirer le changement, dans nos communautés et au-delà.
                  <br>
                  2025 s’ouvre comme une toile vierge, prête à recevoir les œuvres que nous y peindrons ensemble. Nous avons l’opportunité unique d’incarner les idéaux de leadership responsable, d’innovation sociale et de transformation positive.
                  <br>
                  À nos partenaires, votre confiance est essentielle pour réaliser nos projets. À nos visiteurs, votre regard neuf enrichira notre vision. Et à vous, membres de la JCI Nirvana, je vous invite à vous engager pleinement, avec audace et ardeur, autour du thème qui guidera cette aventure.
                  <br>
                  Que cette mandature soit marquée par l’unité, la résilience et des accomplissements qui transcendent les frontières du possible. Ensemble, laissons une empreinte indélébile dans les cœurs et les esprits.
                  <br>
                  Bienvenue à la JCI Nirvana en 2025, où l'excellence devient une manière d'être et d'agir.
                  <br>
                  Avec détermination et humilité,
                  <br>
                  <strong>Saint Christophe MEDA</strong>  <br>
                  <strong>Président Exécutif 2025 de la JCI Nirvana</strong>
                  </p>
               <!--  <a href="page-service-details.html" class="text-theme-colored1 mt-15">Read More <span class="fas fa-long-arrow-alt-right text-theme-colored1 ml-10"></span></a> -->
              </div>
            </div>
          </div>
          {{-- <div class="col-sm-6 col-lg-4">
            <div class="tm-sc-icon-box icon-box text-center iconbox-theme-colored1 animate-icon-on-hover animate-icon-rotate-y mb-30 p-30" data-tm-border="1px solid var(--theme-color1)">
              <div class="icon-box-wrapper">
                <div class="icon-wrapper"><a class="icon icon-lg icon-rounded icon-border-effect effect-rounded"><i class="flaticon-charity-shaking-hands-inside-a-heart"></i> </a></div>
                <div class="icon-text">
                  <h3 class="icon-box-title text-upppercase">Become a Volunteer</h3>
                  <p class="mb-15">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam interdum diam tortor, egestas varius erat aliquam a.</p>
                  <a href="page-become-a-volunteer.html" class="btn btn-xs btn-theme-colored1 btn-outline-light mt-10">Sing Up Today!</a>
                </div>
                <div class="clearfix"></div>
              </div>
            </div>
          </div> --}}
          <div class="col-sm-4 col-lg-4">
            <div class="donate-thumb">
              <img src="{{ URL::asset('assets/front/images/president.png') }}" class="img-fullwidth" alt="">
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="row" id="cadre">
        <div class="col-md-4">
            <div class="bg-white-transparent-8 text-center p-30 pt-20 pb-50 outline-border-5px action">
              <h1 data-tm-font-size="1.5rem" style="font-size: 1.5rem;"> <span class="text-theme-colored1">Vision du mandat 2025</span></h1>
              <p class="" style="text-align: justify"><strong>Bâtissons une communauté d’entraide et de croissance !</strong>  <br>
                L'idée de la vision est de créer une communauté où les membres s'entraident et se soutiennent mutuellement dans leur développement personnel et professionnel, favorisant ainsi la croissance individuelle et collective. C'est un appel à l'action pour construire un environnement où chacun peut contribuer et bénéficier des ressources et de l'expérience des autres membres de la communauté.
                </p>
                <br>
              {{-- <a class="btn btn-theme-colored1 btn-round mt-15" href="#">Read More</a> --}}
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-white-transparent-8 text-center p-30 pt-20 pb-50 outline-border-5px action">
              <h1 data-tm-font-size="1.5rem" style="font-size: 1.5rem;"> <span class="text-theme-colored1">Signe du mandat 2025</span></h1>
              <p class="" style="text-align: justify"><strong>L’engagement des jeunes en vue de la construction d'une JCI Nirvana responsable et durable.</strong>  <br>
                Cette vision met l'accent sur la volonté des jeunes de jouer un rôle actif dans la création et la promotion d'une organisation qui agit de manière responsable envers la société, l'environnement et les individus, en adoptant des pratiques durables et en cherchant à promouvoir l'équité et la justice sociale.
                </p>
                <br>
              {{-- <a class="btn btn-theme-colored1 btn-round mt-15" href="#">Read More</a> --}}
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-white-transparent-8 text-center p-30 pt-20 pb-50 outline-border-5px action">
              <h1 data-tm-font-size="1.5rem" style="font-size: 1.5rem;"><span class="text-theme-colored1">Slogan du mandat 2025</span></h1>
              <p class="" style="text-align: justify"><strong>Éveillons notre potentiel, atteignons le nirvana !</strong> <br>
                Le slogan est une déclaration qui exprime une aspiration à la réalisation de notre plein potentiel et à l'atteinte d'un état de bonheur, de paix et d'épanouissement ultime, souvent associé au concept de nirvana.
                Elle encourage les individus à reconnaître leur potentiel intérieur, à travailler sur eux-mêmes et à poursuivre un cheminement vers un état de réalisation personnelle et de contentement profond. Elle invite à cultiver la croissance personnelle, à développer ses compétences.
                </p>
              {{-- <a class="btn btn-theme-colored1 btn-round mt-15" href="#">Read More</a> --}}
            </div>
        </div>
    </div>
  </section>

  <!-- Section: divider -->
  <section class="divider layer-overlay overlay-white-9" data-tm-bg-img="{{ URL::asset('assets/front/images/slide1.jpg') }}">
    <div class="container pb-90">
      <div class="row">
        <div class="col-lg-6">
          <div class="mb-30">
            <h2 class="line-bottom line-bottom-theme-colored1">Qui sommes-nous?</h2>
            <p>La Jeune Chambre Internationale Nirvana est une organisation de jeunes professionnels et entrepreneurs âgés de 18 à 40 ans et engagés dans le développement personnel et professionnel, ainsi que dans des projets citoyens et économiques. </p>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="mb-30">
            <h2 class="line-bottom line-bottom-theme-colored1">Que faisons-nous?</h2>
            <p>Les membres de la Jeune Chambre Internationale Nirvana participent à des actions locales, nationales et internationales visant à créer un impact positif dans leurs communautés. Ils mettent en œuvre des projets et des initiatives dans des domaines tels que l’entrepreneuriat, le développement durable, l’éducation, la citoyenneté active, la santé, et bien d’autres.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Section: Team -->
  <section class="our-team">
    <div class="container">
      <div class="tm-sc-section-title section-title text-center">
          <div class="title-wrapper">
            <h2 class="title">Le <span class="text-theme-colored1">Comité Directeur Local</span></h2>
            {{-- <p>There are many variations of passages. But the majority have suffered alteration in some form, by injected humour, or randomised words.</p> --}}
          </div>
      </div>
      <div class="container pb-110 pb-lg-50">
          <div class="section-content pb-30">

            <div class="row justify-content-md-center">
              <div class="col-md-6 col-xl-3">
                <div class="tm-sc-team-box">
                  <div class="tm-thumb">
                    <img class="img-fullwidth" src="{{ URL::asset('assets/front/images/pe.jpg') }}" alt="team.jpg">
                  </div>
                  <div class="tm-content text-center">
                    <h5 class="title"><a href="javascript:void(0)">Saint Christophe MEDA</a></h5>
                    <h6 class="position">Président Exécutif</h6>
                    {{-- <div class="team-social">
                      <ul class="styled-icons icon-team-list icon-flat icon-md icon-dark icon-theme-colored1">
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-pinterest-p"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                      </ul>
                    </div> --}}
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-xl-3">
                <div class="tm-sc-team-box">
                  <div class="tm-thumb">
                    <img class="img-fullwidth" src="{{ URL::asset('assets/front/images/vpe.jpg') }}" alt="team.jpg">
                  </div>
                  <div class="tm-content text-center">
                    <h5 class="title"><a href="javascript:void(0)">Wilfried EHOUNOU</a></h5>
                    <h6 class="position">Vice-président Exécutif</h6>
                    {{-- <div class="team-social">
                      <ul class="styled-icons icon-team-list icon-flat icon-md icon-dark icon-theme-colored1">
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-pinterest-p"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                      </ul>
                    </div> --}}
                  </div>
                </div>
              </div>
               <div class="col-md-6 col-xl-3">
                <div class="tm-sc-team-box">
                  <div class="tm-thumb">
                    <img class="img-fullwidth" src="{{ URL::asset('assets/front/images/ipp.jpg') }}" alt="team.jpg">
                  </div>
                  <div class="tm-content text-center">
                    <h5 class="title"><a href="javascript:void(0)">Loulou BEUGRE</a></h5>
                    <h6 class="position">Immediat Past President</h6>
                    {{-- <div class="team-social">
                      <ul class="styled-icons icon-team-list icon-flat icon-md icon-dark icon-theme-colored1">
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-pinterest-p"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                      </ul>
                    </div> --}}
                  </div>
                </div>
              </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-6 col-xl-3">
                    <div class="tm-sc-team-box">
                      <div class="tm-thumb">
                        <img class="img-fullwidth" src="{{ URL::asset('assets/front/images/sg.png') }}" alt="team.jpg">
                      </div>
                      <div class="tm-content text-center">
                        <h5 class="title"><a href="javascript:void(0)">Aicha DIABY</a></h5>
                        <h6 class="position">Secrétaire Général</h6>
                        {{-- <div class="team-social">
                          <ul class="styled-icons icon-team-list icon-flat icon-md icon-dark icon-theme-colored1">
                            <li class="d-block"><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                            <li class="d-block"><a target="_blank" href="#"><i class="fab fa-facebook-f"></i></a></li>
                            <li class="d-block"><a target="_blank" href="#"><i class="fab fa-pinterest-p"></i></a></li>
                            <li class="d-block"><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                          </ul>
                        </div> --}}
                      </div>
                    </div>
                  </div>
              
                  <div class="col-md-6 col-xl-3">
                    <div class="tm-sc-team-box">
                      <div class="tm-thumb">
                        <img class="img-fullwidth" src="{{ URL::asset('assets/front/images/sga.jpg') }}" alt="team.jpg">
                      </div>
                      <div class="tm-content text-center">
                        <h5 class="title"><a href="javascript:void(0)">Patrick PITTE</a></h5>
                        <h6 class="position">Secrétaire Général Adjoint</h6>
                        {{-- <div class="team-social">
                          <ul class="styled-icons icon-team-list icon-flat icon-md icon-dark icon-theme-colored1">
                            <li class="d-block"><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                            <li class="d-block"><a target="_blank" href="#"><i class="fab fa-facebook-f"></i></a></li>
                            <li class="d-block"><a target="_blank" href="#"><i class="fab fa-pinterest-p"></i></a></li>
                            <li class="d-block"><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                          </ul>
                        </div> --}}
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-xl-3">
                    <div class="tm-sc-team-box">
                      <div class="tm-thumb">
                        <img class="img-fullwidth" src="{{ URL::asset('assets/front/images/tg.jpg') }}" alt="team.jpg">
                      </div>
                      <div class="tm-content text-center">
                        <h5 class="title"><a href="javascript:void(0)">Toussaint BEUGRE</a></h5>
                        <h6 class="position">Trésorière Générale</h6>
                        {{-- <div class="team-social">
                          <ul class="styled-icons icon-team-list icon-flat icon-md icon-dark icon-theme-colored1">
                            <li class="d-block"><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                            <li class="d-block"><a target="_blank" href="#"><i class="fab fa-facebook-f"></i></a></li>
                            <li class="d-block"><a target="_blank" href="#"><i class="fab fa-pinterest-p"></i></a></li>
                            <li class="d-block"><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                          </ul>
                        </div> --}}
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 col-xl-3">
                    <div class="tm-sc-team-box">
                      <div class="tm-thumb">
                        <img class="img-fullwidth" src="{{ URL::asset('assets/front/images/cj.jpg') }}" alt="team.jpg">
                      </div>
                      <div class="tm-content text-center">
                        <h5 class="title"><a href="javascript:void(0)">Régine KOUAKOU</a></h5>
                        <h6 class="position">Conseiller Juridique</h6>
                        {{-- <div class="team-social">
                          <ul class="styled-icons icon-team-list icon-flat icon-md icon-dark icon-theme-colored1">
                            <li class="d-block"><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                            <li class="d-block"><a target="_blank" href="#"><i class="fab fa-facebook-f"></i></a></li>
                            <li class="d-block"><a target="_blank" href="#"><i class="fab fa-pinterest-p"></i></a></li>
                            <li class="d-block"><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                          </ul>
                        </div> --}}
                      </div>
                    </div>
                  </div>
            </div>
            <div class="row mt-5">
          
        
              <div class="col-md-6 col-xl-3">
                <div class="tm-sc-team-box">
                  <div class="tm-thumb">
                    <img class="img-fullwidth" src="{{ URL::asset('assets/front/images/ae1.jpg') }}" alt="team.jpg">
                  </div>
                  <div class="tm-content text-center">
                    <h5 class="title"><a href="javascript:void(0)">Fatim YORO</a></h5>
                    <h6 class="position">Assistante Exécutive</h6>
                    {{-- <div class="team-social">
                      <ul class="styled-icons icon-team-list icon-flat icon-md icon-dark icon-theme-colored1">
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-pinterest-p"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                      </ul>
                    </div> --}}
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-xl-3">
                <div class="tm-sc-team-box">
                  <div class="tm-thumb">
                    <img class="img-fullwidth" src="{{ URL::asset('assets/front/images/ae2.jpg') }}" alt="team.jpg">
                  </div>
                  <div class="tm-content text-center">
                    <h5 class="title"><a href="javascript:void(0)">Ange BROU </a></h5>
                    <h6 class="position">Assistante Exécutive</h6>
                    {{-- <div class="team-social">
                      <ul class="styled-icons icon-team-list icon-flat icon-md icon-dark icon-theme-colored1">
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-pinterest-p"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                      </ul>
                    </div> --}}
                  </div>
                </div>
              </div>
   
              <div class="col-md-6 col-xl-3">
                <div class="tm-sc-team-box">
                  <div class="tm-thumb">
                    <img class="img-fullwidth" src="{{ URL::asset('assets/front/images/ae3.jpg') }}" alt="team.jpg">
                  </div>
                  <div class="tm-content text-center">
                    <h5 class="title"><a href="javascript:void(0)">Franck KOUADIO</a></h5>
                    <h6 class="position">Assistant Exécutif</h6>
                    {{-- <div class="team-social">
                      <ul class="styled-icons icon-team-list icon-flat icon-md icon-dark icon-theme-colored1">
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-pinterest-p"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                      </ul>
                    </div> --}}
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-xl-3">
                <div class="tm-sc-team-box">
                  <div class="tm-thumb">
                    <img class="img-fullwidth" src="{{ URL::asset('assets/front/images/vpd.jpg') }}" alt="team.jpg">
                  </div>
                  <div class="tm-content text-center">
                    <h5 class="title"><a href="javascript:void(0)">Florentine KOUAKOU</a></h5>
                    <h6 class="position">Vice-présidente</h6>
                    {{-- <div class="team-social">
                      <ul class="styled-icons icon-team-list icon-flat icon-md icon-dark icon-theme-colored1">
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-pinterest-p"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                      </ul>
                    </div> --}}
                  </div>
                </div>
              </div>
            </div>
            <div class="row mt-5">
              <div class="col-md-6 col-xl-3">
                <div class="tm-sc-team-box">
                  <div class="tm-thumb">
                    <img class="img-fullwidth" src="{{ URL::asset('assets/front/images/vpo.jpg') }}" alt="team.jpg">
                  </div>
                  <div class="tm-content text-center">
                    <h5 class="title"><a href="javascript:void(0)">Joel KOBENAN</a></h5>
                    <h6 class="position">Vice-président</h6>
                    {{-- <div class="team-social">
                      <ul class="styled-icons icon-team-list icon-flat icon-md icon-dark icon-theme-colored1">
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-pinterest-p"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                      </ul>
                    </div> --}}
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-xl-3">
                <div class="tm-sc-team-box">
                  <div class="tm-thumb">
                    <img class="img-fullwidth" src="{{ URL::asset('assets/front/images/as.jpg') }}" alt="team.jpg">
                  </div>
                  <div class="tm-content text-center">
                    <h5 class="title"><a href="javascript:void(0)">Francis KONAN </a></h5>
                    <h6 class="position">Assistant Spécial </h6>
                    {{-- <div class="team-social">
                      <ul class="styled-icons icon-team-list icon-flat icon-md icon-dark icon-theme-colored1">
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-pinterest-p"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                      </ul>
                    </div> --}}
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-xl-3">
                <div class="tm-sc-team-box">
                  <div class="tm-thumb">
                    <img class="img-fullwidth" src="{{ URL::asset('assets/front/images/dircom.jpg') }}" alt="team.jpg">
                  </div>
                  <div class="tm-content text-center">
                    <h5 class="title"><a href="javascript:void(0)">David BONGOUADE</a></h5>
                    <h6 class="position">Directeur de la communication</h6>
                    {{-- <div class="team-social">
                      <ul class="styled-icons icon-team-list icon-flat icon-md icon-dark icon-theme-colored1">
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-pinterest-p"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                      </ul>
                    </div> --}}
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-xl-3">
                <div class="tm-sc-team-box">
                  <div class="tm-thumb">
                    <img class="img-fullwidth" src="{{ URL::asset('assets/front/images/dirproto.jpg') }}" alt="team.jpg">
                  </div>
                  <div class="tm-content text-center">
                    <h5 class="title"><a href="javascript:void(0)">Isaïe SIB</a></h5>
                    <h6 class="position">Directeur du Protocole</h6>
                    {{-- <div class="team-social">
                      <ul class="styled-icons icon-team-list icon-flat icon-md icon-dark icon-theme-colored1">
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-pinterest-p"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                      </ul>
                    </div> --}}
                  </div>
                </div>
              </div>
             
         
            </div>
            <div class="row  mt-5 justify-content-md-center">
              <div class="col-md-6 col-xl-3">
                <div class="tm-sc-team-box">
                  <div class="tm-thumb">
                    <img class="img-fullwidth" src="{{ URL::asset('assets/front/images/dir1.jpg') }}" alt="team.jpg">
                  </div>
                  <div class="tm-content text-center">
                    <h5 class="title"><a href="javascript:void(0)">Abibata TRAORE</a></h5>
                    <h6 class="position">Directrice de Projet</h6>
                    {{-- <div class="team-social">
                      <ul class="styled-icons icon-team-list icon-flat icon-md icon-dark icon-theme-colored1">
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-pinterest-p"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                      </ul>
                    </div> --}}
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-xl-3">
                <div class="tm-sc-team-box">
                  <div class="tm-thumb">
                    <img class="img-fullwidth" src="{{ URL::asset('assets/front/images/dir2.jpg') }}" alt="team.jpg">
                  </div>
                  <div class="tm-content text-center">
                    <h5 class="title"><a href="javascript:void(0)">Dominique EBA</a></h5>
                    <h6 class="position">Directeur de Projet </h6>
                    {{-- <div class="team-social">
                      <ul class="styled-icons icon-team-list icon-flat icon-md icon-dark icon-theme-colored1">
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-pinterest-p"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                      </ul>
                    </div> --}}
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-xl-3">
                <div class="tm-sc-team-box">
                  <div class="tm-thumb">
                    <img class="img-fullwidth" src="{{ URL::asset('assets/front/images/dir3.jpg') }}" alt="team.jpg">
                  </div>
                  <div class="tm-content text-center">
                    <h5 class="title"><a href="javascript:void(0)">Kimpé SIE</a></h5>
                    <h6 class="position">Directeur de Projet</h6>
                    {{-- <div class="team-social">
                      <ul class="styled-icons icon-team-list icon-flat icon-md icon-dark icon-theme-colored1">
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-pinterest-p"></i></a></li>
                        <li class="d-block"><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                      </ul>
                    </div> --}}
                  </div>
                </div>
              </div>
              
            </div>
          </div>
      </div>
    </div>
  </section>

  <!-- Section: divider -->
  <section class="divider parallax bg-img-cover layer-overlay overlay-dark-5" data-tm-bg-img="images/bg/bg13.jpg" data-parallax-ratio="0.3">
    <div class="container pt-50 pb-50">
      <div class="row">
        <div class="col-lg-9">
          <div class="text-center text-lg-start">
            <p class="lead text-white">Rejoignez la Jeune Chambre International Nirvana !
              Explorez, Apprenez, Agissez!
              </p>
          </div>
        </div>
        <div class="col-lg-3">
          <div class="text-center"> <a class="btn btn-sm btn-light btn-round" href="https://jcinirvana.ci/devenir-membre" target="_self">Devenir membre</a></div>
        </div>
      </div>
    </div>
  </section>
     
@endsection
