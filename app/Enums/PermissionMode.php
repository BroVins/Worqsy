<?php
namespace App\Enums;
enum PermissionMode: string { case STANDARD='STANDARD'; case READ_ONLY='READ_ONLY'; case COMMENT_ONLY='COMMENT_ONLY'; case RESTRICTED='RESTRICTED'; }
