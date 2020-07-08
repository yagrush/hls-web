
<h1>Cloud Front Signed Cookie Sample</h1>

<video id="video" muted controls autoplay width="320" height="240">
</video>
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
    var video = document.getElementById('video');
    var videoSrc = 'https://cf.yagrush.net/bipbopall.m3u8'; //**hls**
    if (Hls.isSupported()) {
        var config = {
            debug: true,
            xhrSetup: function (xhr,url) {
                xhr.withCredentials = true;
            }
        };
        var hls = new Hls(config);
        hls.loadSource(videoSrc);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, function() {
            video.play();
        });
    }
    else if (video.canPlayType('application/x-mpegURL')) {
        video.src = videoSrc;
        video.addEventListener('loadedmetadata', function() {
            video.play();
        });
    }
</script>


