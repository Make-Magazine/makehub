<script>
  import { mapState, mapActions } from 'vuex'
  
  import { Filters, PagingControls } from 'vimeography-blueprint';
  import CustomPlayer from './CustomPlayer.vue';

  const template = `
    <div class="vimeography-gallery">
      <filters v-if="this.pro"></filters>
      <custom-player
        v-for="(video, index) in videos"
        v-bind:activeVideo="video"
        v-bind:index="index"
        v-bind:key="video.id">
      </custom-player>
      <paging-controls></paging-controls>
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
        return this.$store.getters.getVideosOnCurrentPage
      }
    },
    components: {
      CustomPlayer,
      Filters,
      "PagingControls": PagingControls.Outline
    }
  }

  export default Gallery;
</script>

<style lang="scss" scoped>
  .vimeography-gallery {
    width: 90%;
    margin: 0 auto 1rem;
  }
</style>
