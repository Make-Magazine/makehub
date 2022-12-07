<script>
  import { mapState } from 'vuex'

  import { Player, Filters, PagingControls, Lightbox } from 'vimeography-blueprint';

  import ThumbnailContainer from './ThumbnailContainer.vue';

  const template = `
    <div class="vimeography-gallery">
      <lightbox layout="geo"></lightbox>
      <filters v-if="this.pro"></filters>
      <thumbnail-container :videos="videos"></thumbnail-container>
      <paging-controls></paging-controls>
    </div>
  `;

  const Gallery = {
    name: 'gallery',
    template,
    computed: {
      ...mapState({
        pro: state => state.gallery.pro,
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
  }
</style>
