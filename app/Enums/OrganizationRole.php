<?php
namespace App\Enums;
enum OrganizationRole: string { case OWNER='OWNER'; case ADMIN='ADMIN'; case MEMBER='MEMBER'; case GUEST='GUEST'; }
