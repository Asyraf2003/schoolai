<div class="article-debug-ruler" aria-hidden="true">
  @for ($x = 1; $x < 20; $x++)
    <span
      class="article-debug-ruler__line article-debug-ruler__line--x{{ $x % 5 === 0 ? ' is-major' : '' }}"
      style="--debug-x: {{ $x }}"
    >
      <b>X{{ $x }}</b>
    </span>
  @endfor

  @for ($y = 1; $y < 12; $y++)
    <span
      class="article-debug-ruler__line article-debug-ruler__line--y{{ $y % 3 === 0 ? ' is-major' : '' }}"
      style="--debug-y: {{ $y }}"
    >
      <b>Y{{ $y }}</b>
    </span>
  @endfor

  <span class="article-debug-ruler__legend">X = kiri→kanan · Y = atas→bawah</span>
</div>
