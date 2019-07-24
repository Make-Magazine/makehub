<script>
import { mapState } from 'vuex'
import { Mixins } from 'vimeography-blueprint'

const defaultTemplate = `
  <figure class="vimeography-thumbnail-wrap">
    <router-link class="vimeography-link" :to="this.query" :title="video.name" exact exact-active-class="vimeography-link-active">

      <div class="vimeography-overlay">
        <span class="vimeography-title">{{video.name}}</span>
        <span class="vimeography-play-icon"></span>
      </div>

      <div class="vimeography-thumbnail">
        <img class="vimeography-thumbnail-img" :src="thumbnailUrl" :alt="video.name" />
      </div>
    </router-link>
  </figure>
`;

const userTemplate = document.querySelector('#vimeography-circles-thumbnail');

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
    display: block;
    border-radius: 50%;
    overflow: hidden;
  }

  .vimeography-link-active {
  }

  .vimeography-overlay {
    overflow: hidden;
    -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
    filter: alpha(opacity=0);
    opacity: 0;
    background: #232323;
    position: absolute;
    border-radius: 50%;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 250ms ease-in-out;
    padding: 1rem;

    &:hover {
      opacity: 0.9;
    }
  }

  .vimeography-play-icon {
    display: block;
    width: 0;
    height: 0;
    border-top: 12px solid transparent;
    border-bottom: 12px solid transparent;
    border-left: 20px solid #fff;
  }

  .vimeography-thumbnail {
    display: block;
    margin: 0 auto;
    padding: 0;
    overflow: hidden;
    cursor: pointer;

    display: flex;
    align-items: center;
    justify-content: center;
  }

  .vimeography-thumbnail-img {
    height: auto;
    max-width: 284px; /* 16 by 9 */
  }

  .vimeography-title {
    flex: 2;
    color: #e4e4e4;
    font-size: 0.9rem;
    line-height: 1.3em;
    padding: 0 0.5rem 0 0;
  }
</style>
