<div class="opinion-card" style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 50%, #dc143c 100%); border-radius: 0.75rem; padding: 0.75rem; box-shadow: 0 10px 25px rgba(106,17,203,0.35); color: #fff; position: relative; overflow: hidden; height: 12.5rem; display: flex; flex-direction: column;">
    <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.15); border-radius: 50%;"></div>
    <div style="position: absolute; bottom: -30px; left: -30px; width: 100px; height: 100px; background: rgba(255,215,0,0.2); border-radius: 50%;"></div>

    @if(session('opinion_submitted'))
        @if(($recentOpinions ?? collect())->isNotEmpty())
            <div style="margin-top: 0.25rem;">
                <p style="font-size: 0.75rem; font-weight: 700; opacity: 0.9; margin-bottom: 0.4rem;">Merci, votre opinion compte pour nous.</p>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.4rem; max-height: 7.5rem; overflow-y: auto; padding-right: 0.25rem;">
                    @foreach($recentOpinions as $op)
                        <div style="background: rgba(255,255,255,0.18); border-radius: 0.5rem; padding: 0.45rem 0.55rem; font-size: 0.72rem; line-height: 1.3; backdrop-filter: blur(4px);">
                            <span style="opacity: 0.95;">{{ $op->content }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
            <span style="background: #FFD700; color: #1a1a2e; font-size: 0.65rem; font-weight: 800; padding: 0.15rem 0.5rem; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.05em; animation: pulse 1.5s infinite;">Nouveau</span>
            <span style="font-size: 0.7rem; opacity: 0.9; font-weight: 600;">Votre avis</span>
        </div>

        <p style="font-weight: 800; font-size: 0.95rem; line-height: 1.3; margin-bottom: 0.75rem; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">
            💬 Votre opinion compte: Qu'aimerez-vous avoir de plus dans xibar360.com?
        </p>

        <form method="POST" action="{{ route('opinions.store') }}" style="position: relative; z-index: 1;">
            @csrf
            <textarea
                name="content"
                id="opinion-content"
                maxlength="250"
                rows="3"
                required
                placeholder="Partagez vos idées, suggestions, fonctionnalités souhaitées..."
                style="width: 100%; border: none; border-radius: 0.5rem; padding: 0.6rem; font-size: 0.85rem; color: #1a1a2e; background: #ffffff; resize: vertical; min-height: 70px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);"
            >{{ old('content') }}</textarea>

            @error('content')
                <p style="color: #FFD700; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
            @enderror

            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.5rem; gap: 0.5rem;">
                <span id="opinion-counter" style="font-size: 0.7rem; opacity: 0.85; font-weight: 600;">0 / 250</span>
                <button type="submit" style="background: #FFD700; color: #1a1a2e; font-weight: 800; font-size: 0.8rem; padding: 0.45rem 1rem; border: none; border-radius: 0.5rem; cursor: pointer; box-shadow: 0 4px 8px rgba(0,0,0,0.2); transition: transform 0.15s;">
                    Envoyer ✨
                </button>
            </div>
        </form>
    @endif
</div>

<style>
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.08); }
    }
    .opinion-card button[type="submit"]:hover { transform: translateY(-2px); }
    .opinion-card textarea:focus { outline: 2px solid #FFD700; }
</style>

<script>
    (function () {
        var ta = document.getElementById('opinion-content');
        var counter = document.getElementById('opinion-counter');
        if (!ta || !counter) return;
        function update() { counter.textContent = ta.value.length + ' / 250'; }
        ta.addEventListener('input', update);
        update();
    })();
</script>
