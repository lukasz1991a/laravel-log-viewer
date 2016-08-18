<?php
namespace Rap2hpoutre\LaravelLogViewer;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Optimal\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Contracts\Auth\Factory as Auth;

class LogViewerController extends Controller
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @param Auth              $auth
     */
    public function __construct(
        Auth $auth
    ) {
        $this->auth = $auth;
    }

    public function index()
    {
        if (Request::input('l')) {
            LaravelLogViewer::setFile(base64_decode(Request::input('l')));
        }

        if (Request::input('dl')) {
            return Response::download(LaravelLogViewer::pathToLogFile(base64_decode(Request::input('dl'))));
        } elseif (Request::has('del')) {
            File::delete(LaravelLogViewer::pathToLogFile(base64_decode(Request::input('del'))));
            return Redirect::to(Request::url());
        }

        $root = $this->auth->user()
            ->roles()
            ->where('alias', 'allLogs')
            ->count();
        $logs = LaravelLogViewer::all($root);
        return View::make('laravel-log-viewer::log', [
            'logs' => $logs,
            'files' => LaravelLogViewer::getFiles(true),
            'current_file' => LaravelLogViewer::getFileName()
        ]);
    }
}
