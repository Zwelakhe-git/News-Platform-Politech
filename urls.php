<?php
require_once __DIR__ . '/config/config.php';
require_once HTDOCS . '/vendor/autoload.php';
require_once __DIR__ . '/controllers.php';

/**
 * @param url - the url pattern
 * @param method - get, pos
 * @param controller - the callback controller
 * @param name - the name of the url pattern
 */
function path_($url, $method, $controller, $name){
    return [
        "url" => $url,
        "method" => $method,
        "handler" => $controller,
        "name" => $name
    ];
}

$urlpatterns = [
    path_('/', 'get', $index, 'index'),
    path_('/news', 'get', $news, 'news'),
    path_('/user/me', 'get', $profile, 'profile'),
    path_('/user/me/:action', 'get', $profile, 'profile'),
    //path_('/user/me/articles', 'get', $author_articles, 'articles'),
    path_('/user/author/admin/:action', 'get',$view_author_admin, 'newsedit'),
    path_('/user/author/admin/:action', 'post', $author_admin, 'admin'),
    path_('/auth/(login|register)', 'get', $login, 'login'),
    path_('/auth/logout', 'get', $logout, 'logout'),
    path_('/auth/(login|register)', 'post', $authenticate, 'auth'),

    /* api urls */
    path_('/api/v1/news/:category/:key/:qty', 'get', $news_api,'newsapi'),
    path_('/api/v1/upload/:type/:action', 'get', $upload_api, 'get_upload_api'),
    path_('/api/v1/upload/:type/:action', 'post', $upload_api, 'post_upload_api'),
    

    // Лайки
    path_('/api/v1/articles/{id}/likes', 'post', $article_api, 'like_article'),
    path_('/api/v1/articles/{id}/likes', 'delete', $article_api, 'unlike_article'),
    path_('/api/v1/articles/{id}/likes/status', 'get', $article_api, 'check_like_status'),

    // Комментарии
    path_('/api/v1/articles/{id}/comments', 'get', $comment_api, 'list_comments'),
    path_('/api/v1/articles/{id}/comments', 'post', $comment_api, 'create_comment'),
    path_('/api/v1/comments/{id}', 'get', $comment_api, 'get_comment'),
    path_('/api/v1/comments/{id}', 'put', $comment_api, 'update_comment'),
    path_('/api/v1/comments/{id}', 'delete', $comment_api, 'delete_comment'),

    // Лайки на комментарии
    path_('/api/v1/comments/{id}/likes', 'post', $comment_api, 'like_comment'),
    path_('/api/v1/comments/{id}/likes', 'delete', $comment_api, 'unlike_comment'),

    /*  */
    path_('*', 'get', function($req, $res){
        $res->status(404);
        $res->send("<h1>ERROR 404</h1>");
    }, 'geterror'),
    path_('*', 'post', function($req, $res){
        $res->status(404);
        $res->send("<h1>ERROR 404</h1>");
    }, 'posterror')
];
?>