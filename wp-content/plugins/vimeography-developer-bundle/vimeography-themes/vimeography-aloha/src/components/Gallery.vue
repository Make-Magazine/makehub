<script>
  import { mapState, mapActions } from 'vuex'

  import { Player, Filters, Lightbox, PagingControls } from 'vimeography-blueprint';

  import ThumbnailContainer from './ThumbnailContainer.vue';

  const template = `
    <div class="vimeography-gallery">
      <lightbox layout="plain-old-player"></lightbox>
      <filters v-if="this.pro"></filters>
      <thumbnail-container :videos="videos" :activeVideoId="this.activeVideo.id"></thumbnail-container>
      <paging-controls></paging-controls>
    </div>
  `;

  const Gallery = {
    name: 'gallery',
    template,
    methods: {
      ...mapActions([
        'loadVideo',
      ]),
    },
    computed: {
      ...mapState({
        activeVideo: state => state.videos.items[state.videos.active],
        pro: state => state.gallery.pro
      }),
      videos() {
        return this.$store.getters.getVideosOnCurrentPage
      }
    },
    components: {
      Player,
      Filters,
      Lightbox,
      ThumbnailContainer,
      "PagingControls": PagingControls.Outline
    }
  }

  export default Gallery;
</script>

<style lang="scss" scoped>
  .vimeography-gallery {
    width: 90%;
    margin: 0 auto 1rem;

    /deep/ .vimeography-player-container {
      margin: 0;
      padding: 0;
    }
  }
</style>
