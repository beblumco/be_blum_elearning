<template>
    <div>
        <!-- Contenedor para el reproductor de YouTube -->
        <div :id="playerId"></div>
    </div>
</template>

<script>

export default {
    props: {
        videoId: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            playerId: 'player-' + Math.random().toString(36).substr(2, 9), // Generar un ID único para el reproductor
            player: null // Referencia al reproductor de YouTube
        };
    },
    mounted() {
        // Cargar la API de YouTube
        const tag = document.createElement('script');
        tag.src = 'https://www.youtube.com/player_api?key=AIzaSyCaI4TOMGdFbROY76hsCY5PNuoBQjIYAd0';
        const firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        // Esperar a que la API de YouTube esté lista
        window.onYouTubePlayerAPIReady = () => {
            this.createPlayer();
        };
    },
    methods: {
        createPlayer() {
            // Crear el reproductor de YouTube
            this.player = new YT.Player(this.playerId, {
                videoId: this.videoId,
                playerVars: {
                    rel: 0,
                },
                events: {
                    onStateChange: this.onPlayerStateChange.bind(this)
                }
            });
        },
        onPlayerStateChange(event) {
            if (event.data === YT.PlayerState.ENDED) {
                // El video ha terminado de reproducirse
                this.$emit('videoFinalizo');
            }
        },
        updateVideo() {
            if (this.player) {
                this.player.loadVideoById(this.videoId);
            }
        },

        pauseVideo() {
            if (this.player && typeof this.player.pauseVideo === 'function') {
                this.player.pauseVideo();
            }
        }
    },
    watch: {
        videoId() {
            this.updateVideo();
        }
    }
}
</script>

<style scoped></style>
