@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'QRypt')
<img src="assets/logo.png" alt="">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
