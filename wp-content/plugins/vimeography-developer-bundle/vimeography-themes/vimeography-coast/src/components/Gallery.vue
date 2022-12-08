<script>
import { mapState, mapActions } from "vuex";

import {
  Player,
  Filters,
  PagingControls,
  Lightbox,
} from "vimeography-blueprint";

import ThumbnailContainer from "./ThumbnailContainer.vue";

const defaultTemplate = `
  <div class="vimeography-gallery">
    <lightbox layout="plain-old-player"></lightbox>
    <filters v-if="this.pro"></filters>
    <thumbnail-container :videos="videos" :activeVideoId="this.activeVideo.id"></thumbnail-container>
  </div>
`;

const userTemplate = document.querySelector("#vimeography-coast-gallery");

const Gallery = {
  name: "gallery",
  template: userTemplate ? userTemplate.innerText : defaultTemplate,
  methods: {
    ...mapActions(["loadVideo"]),
  },
  computed: {
    ...mapState({
      activeVideo: (state) => state.videos.items[state.videos.active],
      galleryId: (state) => state.gallery.id,
      pro: (state) => state.gallery.pro,
    }),
    videos() {
      return this.$store.getters.getVideos;
    },
  },
  components: {
    Player,
    Filters,
    Lightbox,
    ThumbnailContainer,
  },
};

export default Gallery;
</script>

<style lang="scss" scoped>
.vimeography-gallery {
  width: 90%;
  margin: 0 auto 1rem;
}
</style>
