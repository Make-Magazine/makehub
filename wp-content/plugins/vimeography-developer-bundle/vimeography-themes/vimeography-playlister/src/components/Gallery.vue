<script>
  import { mapState, mapActions } from 'vuex'
  
  import { Player, Filters } from 'vimeography-blueprint';

  import ThumbnailContainer from './ThumbnailContainer.vue';

  const template = `
    <div class="vimeography-gallery">
      <filters v-if="this.pro"></filters>
      <div class="vimeography-wrapper">
        <player :activeVideo="this.activeVideo"></player>
        <thumbnail-container :videos="videos" :activeVideoId="this.activeVideo.id"></thumbnail-container>
      </div>
    </div>
  `;

  const Gallery = {
    name: 'gallery',
    template,
    methods: {
      ...mapActions([
        'loadVideo'
      ]),
    },
    watch: {
      '$route' (to, from) {
        const videoId = to.query.vimeography_video;
        const gallery = to.query.vimeography_gallery;

        if (videoId && gallery && gallery == this.galleryId) {
          this.loadVideo(videoId)
        }
      }
    },
    computed: {
      ...mapState({
        activeVideo: state => state.videos.items[state.videos.active],
        galleryId: state => state.gallery.id,
        pro: state => state.gallery.pro
      }),
      videos() {
        return this.$store.getters.getVideos
      }
    },
    components: {
      Player,
      Filters,
      ThumbnailContainer
    }
  }

  export default Gallery;
</script>

<style lang="scss" scoped>
  .vimeography-gallery {
    width: 90%;
    margin: 0 auto 1rem;

    /deep/ .vimeography-player-container {
      margin-bottom: 0;
    }
  }

  .vimeography-wrapper {
    box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.4);
  }

  @media all and (min-width: 940px) {
    .vimeography-wrapper {
      position: relative;
      display: grid;
      grid-template-columns: 55% 45%;
      grid-template-rows: auto;
      grid-auto-rows: 1fr;
    }
  }

  @media all and (-ms-high-contrast: none), (-ms-high-contrast: active), (min-width: 940px) {
    .vimeography-wrapper {
      display: flex;
      flex-wrap: wrap;
    }

    /deep/ .vimeography-player-container {
      flex: 0 1 55%;
    }
  }
</style>
