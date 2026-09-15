@props(['value'])
@php
$map=[
 'APPROVED'=>'green','ON_TRACK'=>'green','ACTIVE'=>'green',
 'OVERDUE'=>'red','CRITICAL'=>'red','BLOCKED'=>'red','REVISION'=>'red','REVISION_REQUIRED'=>'red',
 'AT_RISK'=>'amber','HIGH'=>'amber','WAITING_REVIEW'=>'amber','REVIEWING'=>'amber',
 'DEVELOPMENT'=>'purple','LEAD'=>'purple',
 'CREATE'=>'blue','MAINTENANCE'=>'blue','PROJECT_MANAGER'=>'blue','ADMIN'=>'blue',
 'READ_ONLY'=>'gray','COMMENT_ONLY'=>'gray','MEMBER'=>'gray','GUEST'=>'gray','ASSIGNED'=>'gray',
 'IN_PROGRESS'=>'blue','DONE_SUBMITTED'=>'purple','RESUBMITTED'=>'purple','CLOSED'=>'gray'
];
$key=is_object($value)&&property_exists($value,'value')?$value->value:(string)$value;
$color=$map[$key]??'gray';
@endphp
<span class="badge badge-{{ $color }}">{{ str_replace('_',' ',$key) }}</span>
