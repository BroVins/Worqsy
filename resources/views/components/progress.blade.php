@props(['value'=>0])
<div class="progress" title="{{ number_format((float)$value, 1) }}%">
    <span style="width:{{ max(0,min(100,(float)$value)) }}%"></span>
</div>
