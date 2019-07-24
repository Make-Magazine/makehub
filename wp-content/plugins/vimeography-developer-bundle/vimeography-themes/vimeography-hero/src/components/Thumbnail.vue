<script>
import { mapState } from 'vuex'
import { Mixins, DownloadLink } from 'vimeography-blueprint'

const defaultTemplate = `
  <figure class="vimeography-thumbnail">
    <router-link class="vimeography-link" :to="this.query" :title="video.name" exact exact-active-class="vimeography-link-active" v-on:click.native="scrollToTop">
      <img class="vimeography-thumbnail-img" :src="thumbnailUrl" :alt="video.name" />
    </router-link>
    <figcaption>
      <h2 class="vimeography-title">{{video.name}}</h2>
      <download-link :video="video"></download-link>
    </figcaption>
  </figure>
`;

const userTemplate = document.querySelector('#vimeography-hero-thumbnail');

const Thumbnail = {
  props: ['video'],
  mixins: [Mixins.Thumbnail],
  template: userTemplate ? userTemplate.innerText : defaultTemplate,
  components: {
    DownloadLink
  },
  methods: {
    scrollToTop: function () {
      const galleryId = this.$store.state.gallery.id;
      const player = `#vimeography-gallery-${galleryId} .vimeography-player`;
      document.querySelector(player).scrollIntoView({behavior: "smooth"});
    }
  },
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
  @import url('https://fonts.googleapis.com/css?family=Muli:400,600');

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
    border-radius: 3px;
  }

  .vimeography-link-active {
  }

  .vimeography-thumbnail-img {
    opacity: 0.9;
    transition: all 250ms ease-in-out;
    border-radius: 3px;
    width: 100%;
    padding: 0;
    box-shadow: #000 0em 0em 0em; /* Fixes weird firefox rendering with opacity changes */
    cursor: pointer;

    &:hover {
      opacity: 1;
    }
  }

  figcaption {
    text-align: left;
    position: relative;
    overflow: hidden;
  }

  .vimeography-title,
  /deep/ .vimeography-download {
    color: #222;
    font-family: "Muli", sans-serif;
    font-size: 0.8rem;
    line-height: 1.4rem;
    overflow: hidden;
    font-weight: 600;
    margin: 0;
    padding: 0;
    display: block;
  }
</style>
