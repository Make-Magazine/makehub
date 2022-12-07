<script>
import { mapState, mapActions } from "vuex";
import PlayersContainer from "./PlayersContainer.vue";
import ThumbnailContainer from "./ThumbnailContainer.vue";

const template = `
    <div class="vimeography-gallery">
      <players-container :videos="videos" :activeVideoId="this.activeVideo.id"></players-container>
      <thumbnail-container :videos="videos"></thumbnail-container>
    </div>
  `;

const Gallery = {
  name: "gallery",
  template,
  methods: {
    ...mapActions(["loadVideo"])
  },
  watch: {
    $route(to, from) {
      const videoId = to.query.vimeography_video;
      const gallery = to.query.vimeography_gallery;

      if (videoId && gallery && gallery == this.galleryId) {
        this.loadVideo(videoId);
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
      return this.$store.getters.getVideos.slice(0, 6);
    }
  },
  components: {
    PlayersContainer,
    ThumbnailContainer
  }
};

export default Gallery;
</script>

<style lang="scss" scoped>
.vimeography-gallery {
  width: 90%;
  margin: 0 auto 1rem;

  position: relative;
  display: flex;
  flex-direction: column;
}

@media all and (min-width: 600px) {
  .vimeography-gallery {
    display: grid;
    grid-template-columns: 75% 25%;
    grid-column-gap: 30px;
  }
}
</style>
