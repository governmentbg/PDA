
<div id="accessibility-bubble">
  <i class="fa-solid fa-universal-access"></i>
</div>


<div id="accessibility-sidebar">
    <h5>{{ __('footer.accessibility') }}</h5>
    <button id="increase-font" class="btn">{{ __('footer.increment_font') }}</button>
    <button id="decrease-font" class="btn">{{ __('footer.decrement_font') }}</button>
    <button id="toggle-text-version" class="btn">{{ __('footer.text_version') }}</button>
    <button id="dyslexia-mode" class="btn">{{ __('footer.dyslexia_mode') }}</button>
    <button id="toggle-reset" class="btn">{{ __('footer.reset') }}</button>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap');

    #accessibility-bubble {
        position: fixed;
        right: 80px;
        bottom: 20px;
        background: #fff;
        color: #007bff;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10000;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        font-weight: bold;
        font-size: 50px;
        transition: all 0.3s ease;
    }

    #accessibility-bubble.active {
    background: #ff8c00;
    color: #fff;
    }

    /* #accessibility-bubble:hover {
        box-shadow: 0 6px 16px rgba(0,0,0,0.4);
        transform: scale(1.1);
    } */

    #accessibility-sidebar {
        position: fixed;
        right: 10px;
        bottom: 80px;
        width: 220px;
        background: #f4f4f4;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        display: none;
        z-index: 9999;
        transition: all 0.3s ease;
    }
    #accessibility-sidebar h5 {
        margin-top: 0;
        margin-bottom: 10px;
        text-align: center;
        color: #333;
    }
    #accessibility-sidebar .btn {
        width: 100%;
        margin-bottom: 5px;
        cursor: pointer;
        padding: 5px 0;
        font-size: 14px;
    }
    #accessibility-sidebar .btn.active {
        background: #0d6efd;;
        color: #fff;
        font-weight: 600;
    }


    .text-only-mode img,
    .text-only-mode video,
    .text-only-mode iframe,
    .text-only-mode object,
    .text-only-mode a.thumb-card,
    .text-only-mode svg {
        display: none !important;
    }
    .text-only-mode .object-img {
        display: none !important;
    }
    .text-only-mode body,
    .text-only-mode main {
        background: #fff !important;
        color: #000 !important;
        line-height: 1.6;
    }

    .dyslexia-mode {
        font-family: 'Open Sans', Arial, sans-serif !important;
        line-height: 1.8 !important;
        letter-spacing: 0.5px;
    }
</style>

<script>
    $(document).ready(function() {
    var $body = $('body');
    var $content = $('main').length ? $('main') : $body;

    var elements = $('h1,h2,h3,h4,h5,h6,p,a,span,li,td,th');
    var originalSizes = [];
    elements.each(function() {
        originalSizes.push(parseFloat($(this).css('font-size')));
    });

    var totalIncrement = parseInt(localStorage.getItem('fontIncrement')) || 0;
    var textVersion = localStorage.getItem('textVersion') === 'true';
    var dyslexiaMode = localStorage.getItem('dyslexiaMode') === 'true';

       function updateButtonStates() {
            $('#increase-font, #decrease-font, #toggle-text-version, #dyslexia-mode').removeClass('active');

            let hasActiveMode = false;

            if (totalIncrement > 0) {
                $('#increase-font').addClass('active');
                hasActiveMode = true;
            } else if (totalIncrement < 0) {
                $('#decrease-font').addClass('active');
                hasActiveMode = true;
            }

            if ($body.hasClass('text-only-mode')) {
                $('#toggle-text-version').addClass('active');
                hasActiveMode = true;
            }

            if ($body.hasClass('dyslexia-mode')) {
                $('#dyslexia-mode').addClass('active');
                hasActiveMode = true;
            }

            if (hasActiveMode) {
                $('#accessibility-bubble').addClass('active');
            } else {
                $('#accessibility-bubble').removeClass('active');
            }
        }

    if (totalIncrement !== 0) {
        elements.each(function(index) {
            $(this).css('font-size', (originalSizes[index] + totalIncrement) + 'px');
        });
    }

    if (textVersion) {
        $body.addClass('text-only-mode');
    }

    if (dyslexiaMode) {
        $body.addClass('dyslexia-mode');
    }

    updateButtonStates();

    $('#accessibility-bubble').click(function() {
        $('#accessibility-sidebar').fadeToggle(300);
    });


    $('#increase-font').click(function() {
        totalIncrement += 2;

        localStorage.setItem('fontIncrement', totalIncrement);

        elements.each(function(index) {
            $(this).css('font-size', (originalSizes[index] + totalIncrement) + 'px');
        });

        updateButtonStates();
    });

    $('#decrease-font').click(function() {
        totalIncrement -= 2;

        localStorage.setItem('fontIncrement', totalIncrement);

        elements.each(function(index) {
            var newSize = originalSizes[index] + totalIncrement;
            if (newSize < 12) newSize = 12;
            $(this).css('font-size', newSize + 'px');
        });

        updateButtonStates();
    });


    $('#toggle-text-version').click(function() {
        $body.toggleClass('text-only-mode');
        var isActive = $body.hasClass('text-only-mode');
        localStorage.setItem('textVersion', isActive);

        updateButtonStates();
    });

    $('#dyslexia-mode').click(function() {
        $body.toggleClass('dyslexia-mode');
        var isActive = $body.hasClass('dyslexia-mode');
        localStorage.setItem('dyslexiaMode', isActive);

        updateButtonStates();
    });


    $('#toggle-reset').click(function() {
        localStorage.setItem('fontIncrement', 0);
        localStorage.setItem('textVersion', false);
        localStorage.setItem('dyslexiaMode', false);

        $body.removeClass('text-only-mode dyslexia-mode');

        elements.each(function(index) {
            $(this).css('font-size', originalSizes[index] + 'px');
        });

        totalIncrement = 0;

        updateButtonStates();
    });
});
</script>
