@props(['type' => 'card', 'lines' => 3])

@if($type === 'card')
    <div class="skeleton-card">
        <div class="skeleton skeleton-title"></div>
        @for($i = 0; $i < $lines; $i++)
            <div class="skeleton skeleton-text"></div>
        @endfor
    </div>
@elseif($type === 'table')
    <div class="modern-table">
        <table class="table">
            <thead>
                <tr>
                    <th><div class="skeleton skeleton-text" style="width: 100px;"></div></th>
                    <th><div class="skeleton skeleton-text" style="width: 150px;"></div></th>
                    <th><div class="skeleton skeleton-text" style="width: 120px;"></div></th>
                    <th><div class="skeleton skeleton-text" style="width: 100px;"></div></th>
                </tr>
            </thead>
            <tbody>
                @for($i = 0; $i < 5; $i++)
                <tr>
                    <td><div class="skeleton skeleton-text"></div></td>
                    <td><div class="skeleton skeleton-text"></div></td>
                    <td><div class="skeleton skeleton-text"></div></td>
                    <td><div class="skeleton skeleton-text"></div></td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>
@else
    <div class="skeleton skeleton-text"></div>
@endif




