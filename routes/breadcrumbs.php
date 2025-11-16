<?php

    use App\Models\Article;
    use App\Models\Activity;
    use Diglactic\Breadcrumbs\Breadcrumbs;
    use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
    // home

    Breadcrumbs::for('home', function ($trail) {
        $trail->push('Accueil', route('home'));
    });

    Breadcrumbs::for('presentation', function ($trail) {
        $trail->parent('home');

        $trail->push('Présentation', route('presentation'));
    });

    Breadcrumbs::for('posts', function ($trail) {
        $trail->parent('home');

        $trail->push('Actualité', route('posts'));
    });

    Breadcrumbs::for('activities', function ($trail) {
        $trail->parent('home');

        $trail->push('Activités', route('activities'));
    });

    Breadcrumbs::for('public.gallery', function ($trail) {
        $trail->parent('home');

        $trail->push('Galérie', route('public.gallery'));
    });

    /* Breadcrumbs::for('news', function (BreadcrumbTrail $trail): void {
        $trail->parent('accueil');

        $trail->push('Actualité', route('news'));
    }); */

    Breadcrumbs::for('contact', function (BreadcrumbTrail $trail): void {
        $trail->parent('home');
        $trail->push('contact', route('contact'));
    });

    Breadcrumbs::for('public.single-post', function ($trail, Article $article) {
        $trail->parent('posts');
        $trail->push($article->title, route('public.single-post', ['slug' => $article->slug]));
    });

    Breadcrumbs::for('public.single-activity', function ($trail, Activity $activity) {
        $trail->parent('activities');
        $trail->push($activity->title, route('public.single-activity', ['id' => $activity->id, 'title' => str_slug($activity->title) ]));
    });


