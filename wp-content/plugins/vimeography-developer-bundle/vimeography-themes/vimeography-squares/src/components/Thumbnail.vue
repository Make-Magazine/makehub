<script>
import { mapState } from 'vuex'
import { Mixins } from 'vimeography-blueprint'

const defaultTemplate = `
  <figure class="vimeography-thumbnail">
    <img class="vimeography-thumbnail-img" :src="thumbnailUrl" :alt="video.name" />

    <figcaption>
      <router-link class="vimeography-link" :to="this.query" :title="video.name" exact exact-active-class="vimeography-link-active">
        <h2 class="vimeography-title">{{video.name}}</h2>
        <div class="vimeography-description" v-html="video.description"></div>
      </router-link>
    </figcaption>
  </figure>
`;

const userTemplate = document.querySelector('#vimeography-squares-thumbnail');

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
    overflow: hidden;
    cursor: pointer;
    position: relative;
  }

  /* IE11 Support */
  @media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
    .vimeography-thumbnail {
      flex: 0 0 25%;
    }
  }

  .vimeography-thumbnail-img {
    object-fit: cover;
    width: 100%;
    height: 100%;
  }

  .vimeography-link {
    display: block;
    text-decoration: none;
    position: relative;
    box-sizing: border-box;
    padding: 10px;
    height: 100%;
  }

  .vimeography-link-active {
  }

  figcaption {
    background: none repeat scroll 0 0 rgba(255, 255, 255, 0.85);
    -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#D8FFFFFF,endColorstr=#D8FFFFFF), alpha(opacity=0)"; /* IE8 */
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#D8FFFFFF,endColorstr=#D8FFFFFF), alpha(opacity=0);   /* IE6 & 7 */
          zoom: 1;
    color: #222;
    box-sizing: border-box;
    margin: 0;
    opacity: 0;
    overflow: hidden;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    text-align: left;
    transition: all 150ms ease-in-out;

    &:hover {
      opacity: 1;
    }

    &:after {
      content: '';
      position: absolute;
      bottom: 0;
      width: 100%;
      height: 25px;
      background: linear-gradient(rgba(255, 255, 255, 0.001), #fff); /* transparent keyword is broken in Safari */
      pointer-events: none;
    }
  }

  .vimeography-title {
    color: #333;
    font-size: 1rem;
    line-height: 1rem;
    margin: 0 0 0.5rem;
    padding: 0;
  }

  .vimeography-description {
    font-size: 0.75rem;
    margin: 0;
    overflow: hidden;
    color: #333;
    line-height: 1.1rem;
  }
</style>
