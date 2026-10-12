<?php

namespace App\Http\Controllers;

use App\Application\Portal\DescribePlaceholderPage;
use App\Domain\Portal\PortalPage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class PortalPlaceholderController extends Controller
{
    public function __invoke(Request $request, DescribePlaceholderPage $pages): View
    {
        $page = match ($request->route()?->getName()) {
            'artikel' => PortalPage::Article,
            'galeri' => PortalPage::Gallery,
            'admin.dashboard' => PortalPage::Admin,
            'guru.dashboard' => PortalPage::Teacher,
            'murid.dashboard' => PortalPage::Student,
            default => abort(404),
        };

        return view('portal.placeholder', [
            'page' => $pages->execute($page),
        ]);
    }
}
