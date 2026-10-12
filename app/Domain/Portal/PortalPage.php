<?php

namespace App\Domain\Portal;

enum PortalPage: string
{
    case Article = 'article';
    case Gallery = 'gallery';
    case Admin = 'admin';
    case Teacher = 'teacher';
    case Student = 'student';
}
