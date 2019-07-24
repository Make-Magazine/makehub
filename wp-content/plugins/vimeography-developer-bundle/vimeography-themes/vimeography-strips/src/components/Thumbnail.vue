<script>
import { mapState } from 'vuex'
import { Mixins } from 'vimeography-blueprint'

const defaultTemplate = `
  <figure class="vimeography-thumbnail">
    <router-link class="vimeography-link" :to="this.query" :title="video.name" exact exact-active-class="vimeography-link-active">
      <img class="vimeography-thumbnail-img" :src="thumbnailUrl" :alt="video.name" />
    </router-link>
    <figcaption>
      <h2 class="vimeography-title">{{video.name}}</h2>
      <p class="vimeography-description" v-html="video.description"></p>
    </figcaption>
  </figure>
`;

const userTemplate = document.querySelector('#vimeography-strips-thumbnail');

const Thumbnail = {
  props: ['video'],
  mixins: [Mixins.Thumbnail],
  template: userTemplate ? userTemplate.innerText : defaultTemplate,
  computed: {
    query() {
      const q = {
        ...this.$route.query,
        vimeography_gallery: this.$store.state.gallery.id,
        vimeography_video: this.video.id
      };

      return '?' + Object.keys(q).map(k => k + '=' + encodeURIComponent(q[k])).join('&')
    },
    ...mapState({
      galleryId: state => state.id
    })
  }
}

export default Thumbnail;
</script>

<style lang="scss" scoped>
  .vimeography-thumbnail {
    margin: 0;
    width: 100%;
  }

  /* IE11 Support */
  @media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
    .vimeography-thumbnail {
      flex: 0 0 25%;
    }
  }

  .vimeography-link {
    display: block;
    margin: 0 0 20px;
  }

  .vimeography-link-active {
  }

  .vimeography-thumbnail-img {
    opacity: 0.9;
    transition: all 250ms ease-in-out;

    width: 100%;
    padding: 0;
    box-shadow: #000 0em 0em 0em; /* Fixes weird firefox rendering with opacity changes */
    cursor: pointer;

    &:hover {
      opacity: 1;
    }
  }

  figcaption {
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .vimeography-title {
    color: #222;
    font-family: sans-serif;
    font-size: 11px;
    line-height: 1.2em;
    overflow: hidden;
    font-weight: 500;
    margin: 0 0 8px;
    display: block;
  }

  .vimeography-description {
    color: #aaa;
    font-family: serif;
    font-size: 11px;
    line-height: 1.2em;
    font-weight: 300;
    text-transform: lowercase;
    padding: 0;
    margin: 0;

    display: block;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
  }
</style>
