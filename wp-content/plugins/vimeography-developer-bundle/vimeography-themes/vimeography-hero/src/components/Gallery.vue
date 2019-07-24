<script>
  import { mapState, mapActions } from 'vuex'
  
  import { Player, Filters, PagingControls } from 'vimeography-blueprint';

  import ThumbnailContainer from './ThumbnailContainer.vue';

  const template = `
    <div class="vimeography-gallery">
      <filters v-if="this.pro"></filters>
      <player :activeVideo="this.activeVideo"></player>
      <div class="vimeography-info">
        <h2 class="vimeography-title">{{this.activeVideo.name}}</h2>
        <div class="vimeography-description" v-html="this.activeVideo.description"></div>
      </div>
      <thumbnail-container :videos="videos"></thumbnail-container>
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
      Player,
      Filters,
      ThumbnailContainer,
      "PagingControls": PagingControls.Outline
    }
  }

  export default Gallery;
</script>

<style lang="scss" scoped>
  @import url('https://fonts.googleapis.com/css?family=Muli:400,600');

  .vimeography-gallery {
    width: 90%;
    margin: 0 auto 1rem;
  }

  /deep/ .vimeography-player-container {
    margin-bottom: 2rem !important;
  }

  .vimeography-info {
    text-align: center;
  }

  .vimeography-title {
    color: #222;
    font-family: "Muli", sans-serif;
    font-size: 1.4rem;
    line-height: 1.4rem;
    overflow: hidden;
    font-weight: 600;
    margin: 0;
    padding: 0;
    display: block;

    &:after {
      content: '';
      width: 30px;
      height: 1px;
      background-color: #999;
      display: block;
      margin: 1rem auto;
    }
  }

  .vimeography-description {
    font-family: "Muli", sans-serif;
    font-size: 1rem;
    line-height: 1.4rem;
    padding-bottom: 1rem;
    max-width: 25rem;
    margin: 0 auto 2rem;
  }
</style>
