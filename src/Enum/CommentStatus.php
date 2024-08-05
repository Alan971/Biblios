<?php

namespace App\Enum;

enum CommentStatus: string
{
    case Pending = 'Pending';
    case Published = 'Published';
    case Moderated = 'Moderated';
    
    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'En atttente',
            self::Published => 'Publié',
            self::Moderated => 'Modéré',
        };
    }
}