<script>
import { mapState } from "vuex";
import { Mixins } from "vimeography-blueprint";

const defaultTemplate = `
  <figure :class="this.thumbnailClass">
    <router-link class="vimeography-link" :title="video.name" :to="this.query" exact exact-active-class="vimeography-link-active">
      <h2 class="vimeography-title">{{video.name}}</h2>
      <img loading="lazy" class="vimeography-thumbnail-img" :src="thumbnailUrl" :alt="video.name" :width="video.width" :height="video.height" />
    </router-link>
  </figure>
`;

const userTemplate = document.querySelector("#vimeography-coast-thumbnail");

const Thumbnail = {
  props: ["video"],
  mixins: [Mixins.Thumbnail],
  template: userTemplate ? userTemplate.innerText : defaultTemplate,
  computed: {
    thumbnailClass() {
      return `swiper-slide vimeography-thumbnail vimeography-video-${this.video.id}`;
    },
    query() {
      const q = {
        ...this.$route.query,
        vimeography_gallery: this.$store.state.gallery.id,
        vimeography_video: this.video.id,
      };

      return (
        "?" +
        Object.keys(q)
          .map((k) => k + "=" + encodeURIComponent(q[k]))
          .join("&")
      );
    },
    ...mapState({
      galleryId: (state) => state.id,
    }),
  },
};

export default Thumbnail;
</script>

<style lang="scss" scoped>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@600&display=swap");

.vimeography-thumbnail {
  margin: 0;
  max-width: 300px;
}

.vimeography-link {
  display: block;
  font-size: 0;
  line-height: 0;
  border-radius: 4px;
  box-shadow: none;
  position: relative;

  img {
    border: 1px solid #cccccc;
  }
}

.vimeography-link-active img {
  border: 1px solid #5580e6;
}

.vimeography-thumbnail-img {
  border-radius: 4px;
  max-width: 298px;
  cursor: pointer;
  height: auto;
}

.vimeography-title {
  position: absolute;
  box-sizing: border-box;
  display: block;
  width: 100%;
  bottom: 0;
  padding: 15px;
  font-size: 20px;
  line-height: 24px;
  text-align: left;
  margin: 0;
  color: #fff;
  text-shadow: 0 2px 2px rgba(45, 46, 51, 0.65);
  font-family: "Inter", sans-serif;
  font-weight: 600;
  letter-spacing: 0.2px;
}
</style>
