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

const userTemplate = document.querySelector('#vimeography-bridge-thumbnail');

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
    max-width: 100%;
  }

  /* IE11 Support */
  @media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
    .vimeography-thumbnail {
      flex: 0 0 25%;
    }
  }

  .vimeography-link {
    display: block;
    font-size: 0;
    line-height: 0;

    border: 2px solid #dadada;
    overflow: hidden;
    cursor: pointer;
    margin: 0 0 15px;
    border-radius: 5px;

    &:hover {
      box-shadow: 0px 2px 10px #888;
      border: 2px solid #fff;
      border-radius: 2px;
    }
  }

  .vimeography-link-active {
    box-shadow: 0px 2px 10px #888;
    border: 2px solid #2D86FF;
    border-radius: 2px;
  }

  .vimeography-thumbnail-img {
    max-width: 100%;
    cursor: pointer;
    width: 100%;
  }

  .vimeography-title {
    font-size: 1rem;
    line-height: 1.3rem;

    text-align: left;
    font-family: Arial, sans-serif;
    font-weight: bold;
    color: #565656;
    overflow: hidden;
    background: none;
  }

  .vimeography-description {
    text-overflow: ellipsis;
    height: 200px;
    white-space: nowrap;
    overflow: hidden;

    position: relative;
    text-align: left;
    height: 3rem;
    overflow: hidden;
    margin: 0;
    padding: 0;
    line-height: 1.5em;
    display: block;
    font-size: 11px;
    font-family: georgia, serif;
    color: #555;
  }
</style>
