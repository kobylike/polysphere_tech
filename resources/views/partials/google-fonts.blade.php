]633;E;{ echo "{{-- Non-blocking Google Fonts --}}"\x3b while IFS= read -r u\x3b do   case "$u" in *display=*) \x3b\x3b *) u="$u&display=swap" \x3b\x3b esac\x3b   echo "<link rel=\\"stylesheet\\" href=\\"$u\\" media=\\"print\\" onload=\\"this.media='all'\\">"\x0adone <<< "$URLS"\x3b echo "<noscript>"\x3b while IFS= read -r u\x3b do   case "$u" in *display=*) \x3b\x3b *) u="$u&display=swap" \x3b\x3b esac\x3b   echo "    <link rel=\\"stylesheet\\" href=\\"$u\\">"\x0adone <<< "$URLS"\x3b echo "</noscript>"\x3b } > resources/views/partials/google-fonts.blade.php;5b6f1143-09f9-4239-b010-154c45ab4b6a]633;C{{-- Non-blocking Google Fonts --}}
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" media="print" onload="this.media='all'">
<noscript>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap">
</noscript>
