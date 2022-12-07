<script>
import { mapState } from "vuex";
import { Mixins } from "vimeography-blueprint";

const defaultTemplate = `
  <figure class="swiper-slide vimeography-thumbnail">
    <img class="vimeography-thumbnail-img" :src="thumbnailUrl" :alt="video.name" />

    <router-link class="vimeography-link" :to="this.query" :title="video.name" exact exact-active-class="vimeography-link-active">
      <span>{{video.name}}</span>
    </router-link>
  </figure>
`;

const userTemplate = document.querySelector("#vimeography-journey-thumbnail");

const Thumbnail = {
  props: ["video"],
  mixins: [Mixins.Thumbnail],
  template: userTemplate ? userTemplate.innerText : defaultTemplate,
  computed: {
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
.vimeography-thumbnail.vimeography-thumbnail {
  width: 95px;
  height: 95px;
  margin: 0;

  zoom: 1;
  overflow: hidden;
  border-radius: 2px;
  position: relative;

  display: flex;
  align-items: center;
  justify-content: center;
}

.vimeography-link {
  display: block;

  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  opacity: 0;
  background: rgba(0, 0, 0, 0.75);
  padding: 5px;
  text-decoration: none;
  font-weight: 500;
  outline: none;
  transition: opacity 0.2s ease-in-out;

  &:hover {
    opacity: 1;
  }

  span {
    color: #fff;
    font-size: 0.8rem;
    line-height: 1rem;
  }
}

.vimeography-link-active {
  opacity: 1;
}

.vimeography-thumbnail-img {
  cursor: pointer;

  max-width: 169px;
  position: absolute;
}
</style>
