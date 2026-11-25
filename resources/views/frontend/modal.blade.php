<div class="modal fade videomodal" id="videoModal{{ $video['id'] }}" tabindex="-1"
    aria-labelledby="videoModalLabel{{ $video['id'] }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title theme-text-color" id="videoModalLabel{{ $video['id'] }}">{{ $video['title'] }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <video id="video_player_{{ $video['id'] }}" class="video-js vjs-default-skin w-100" controls preload="auto"
                    width="100%" height="360" data-setup='{"playbackRates": [0.5, 1, 1.25, 1.5, 2],"preferFullWindow":true,"responsive":true}'>
                    <source src="{{ $video['src'] }}" type="video/mp4" />
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>
</div>
