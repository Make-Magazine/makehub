<script>
import { mapState } from "vuex";
import { Mixins } from "vimeography-blueprint";

const defaultTemplate = `
  <figure class="vimeography-thumbnail">
    <figcaption>
      <router-link class="vimeography-link" :to="this.query" :title="video.name" exact exact-active-class="vimeography-link-active">
        <div class="vimeography-thumbnail-image-wrapper">
          <img class="vimeography-thumbnail-img" v-lazy="thumbnailUrl" :alt="video.name" />
        </div>
        <h2 class="vimeography-title">{{video.name}}</h2>
      </router-link>
    </figcaption>
  </figure>
`;

const userTemplate = document.querySelector("#vimeography-covers-thumbnail");

const Thumbnail = {
  props: ["video"],
  mixins: [Mixins.Thumbnail],
  template: userTemplate ? userTemplate.innerText : defaultTemplate,
  computed: {
    query() {
      const q = {
        ...this.$route.query,
        vimeography_gallery: this.$store.state.gallery.id,
        vimeography_video: this.video.id
      };

      return (
        "?" +
        Object.keys(q)
          .map(k => k + "=" + encodeURIComponent(q[k]))
          .join("&")
      );
    },
    ...mapState({
      galleryId: state => state.id
    })
  }
};

export default Thumbnail;
</script>

<style lang="scss" scoped>
.vimeography-thumbnail {
  margin: 0;
  padding: 0;
  cursor: pointer;
  position: relative;
}

/* IE11 Support */
@media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
  .vimeography-thumbnail {
    flex: 0 0 25%;
  }
}

.vimeography-thumbnail-image-wrapper {
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 2px;
}

.vimeography-thumbnail-img {
  max-width: 496px;
  height: 280px;
}

.vimeography-link {
  display: block;
  text-decoration: none;
  position: relative;
  box-sizing: border-box;
}

.vimeography-link-active {
}

figcaption {
  background: none repeat scroll 0 0 rgba(255, 255, 255, 0.85);
  color: #222;
  box-sizing: border-box;
  margin: 0;
  text-align: left;
  transition: all 150ms ease-in-out;
  opacity: 0.9;

  &:hover {
    opacity: 1;
  }
}

.vimeography-title {
  color: #333;
  font-size: 1rem;
  line-height: 1rem;
  margin: 0;
  padding: 0.5rem 0;
}
</style>
