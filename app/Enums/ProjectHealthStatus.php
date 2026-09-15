<?php
namespace App\Enums;
enum ProjectHealthStatus: string { case ON_TRACK='ON_TRACK'; case AT_RISK='AT_RISK'; case BLOCKED='BLOCKED'; case OVERDUE='OVERDUE'; case WAITING_REVIEW='WAITING_REVIEW'; case REVISION_REQUIRED='REVISION_REQUIRED'; }
