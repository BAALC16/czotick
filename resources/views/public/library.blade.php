@extends('public.layouts_index')
@section('style')
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }

        table td {
            word-wrap: break-word !important;
        }

        table thead th {
            padding: 12px 25px;
            font-weight: 700;
            text-align: left;
            font-size: 20px;
        }

        table tbody tr {
            background: #f3f6f9;
        }

        table tbody tr td, table tbody tr th {
            padding: 18px 40px;
            font-weight: normal;
            text-align: left;
        }

        table tbody tr:nth-child(2n) {
            background: #ffffff;
        }

        a {
            color: #000000;
        }

        .btn-link a{
            font-size: 15px;
            line-height: 26px;
            font-weight: 600;
            color: #0097dc;
            border: 1px solid #ffffff;
            background: #ffffff;
            padding: 12px 30px;
            border-radius: 25px;
            border: 2px solid #0097dc;
            text-transform: uppercase;
            font-family: 'Poppins', sans-serif;
        }

        .btn-link a:hover{
            color: #ffffff;
            border-color: #0097dc;
            background: #0097dc;
            text-decoration: none;
            transition: all 500ms ease;
            -moz-transition: all 500ms ease;
            -webkit-transition: all 500ms ease;
        }

    </style>
@endsection
@section('content')
    <section class="page-title" style="background-image:url(images/background/20.jpg);">
        <div class="container">
            <div class="title-text clearfix">
                <h1>Bibliothèque</h1>
                <ul class="title-menu">
                    <li><a href="/">Accueil</a><i class="fa fa-angle-right" aria-hidden="true"></i></li>
                    <li>Bibliothèque</li>
                </ul>
            </div>
        </div>
    </section>
    <!--news-section-->
    <section class="news-section">
        <div class="container">
            <div class="row text-center">
                <table>
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse ( $libraries as  $library)
                            <tr>
                                <td class="one"><a href="{{ url('/public/storage/'.$library->url) }}">{{ $library->title }}</a></td>
                                <td class="text-right">
                                    <div class="btn-link">
                                        <a href="{{ url('/public/storage/'.$library->url) }}">Télécharger</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">Aucun document disponible</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>



            </div>
        </div>
    </section>
    <!--End news-section-->

@endsection

