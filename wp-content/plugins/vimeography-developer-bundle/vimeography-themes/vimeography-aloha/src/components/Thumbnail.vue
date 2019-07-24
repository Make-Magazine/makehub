<script>
import { mapState } from 'vuex'
import { Mixins } from 'vimeography-blueprint'

const defaultTemplate = `
  <figure class="vimeography-thumbnail-wrap">
    <router-link class="vimeography-link" :to="this.query" :title="video.name" exact exact-active-class="vimeography-link-active">
      <span class="vimeography-title">{{video.name}}</span>

      <div class="vimeography-thumbnail">
        <img class="vimeography-thumbnail-img" :src="thumbnailUrl" :alt="video.name" />
      </div>
    </router-link>
  </figure>
`;

const userTemplate = document.querySelector('#vimeography-aloha-thumbnail');

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
  .vimeography-thumbnail-wrap {
    margin: 0;
    max-width: 100%;
    position: relative;
  }

  /* IE11 Support */
  @media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
    .vimeography-thumbnail-wrap {
      flex: 0 0 25%;
    }
  }

  .vimeography-link {
    cursor: pointer;
    display: block;
    background-color: #4C4C4C;
    box-shadow: 1px 1px 2px #A0A0A0;
    overflow: hidden;
    position: relative;

    &:hover {
      box-shadow: 1px 1px 2px #A0A0A0;

      .vimeography-thumbnail-img {
        transform: translateY(-25px);
        opacity: 1;
      }
    }
  }

  .vimeography-link-active {
  }

  .vimeography-thumbnail {
    height: 100%;
    overflow: hidden;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .vimeography-thumbnail-img {
    cursor: pointer;
    max-width: 231px;
    opacity: 0.9;
    transition: all 250ms ease-in-out;
    box-shadow: none;
  }

  .vimeography-title {
    position: absolute;
    bottom: 0;
    left: 3px;
    height: 23px;
    width: auto;
    color: #f4f4f4;
    font-size: 0.8rem;
    text-transform: uppercase;
    z-index: 0;
    opacity: 0;
    line-height: 1.4rem;
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
    -webkit-font-smoothing: antialiased;
    transition: all 250ms ease-in-out;
  }

  .vimeography-link:hover .vimeography-title {
    opacity: 1;
  }
</style>
