<script>
import { mapState } from 'vuex'
import { Mixins } from 'vimeography-blueprint'

const defaultTemplate = `
  <figure class="vimeography-thumbnail">
    <img class="vimeography-thumbnail-img" :src="thumbnailUrl" :alt="video.name" />

    <figcaption>
      <h2 class="vimeography-title">{{video.name}}</h2>
      <p class="vimeography-subtitle">{{video.human_created_time}}</p>
      <router-link class="vimeography-link" :to="this.query" :title="video.name" exact exact-active-class="vimeography-link-active">View more
      </router-link>
    </figcaption>
  </figure>
`;

const userTemplate = document.querySelector('#vimeography-timber-thumbnail');

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
    position: relative;
    z-index: 1;
    overflow: hidden;
    background: #3085a3;
    text-align: center;
    cursor: pointer;
    border-radius: 4px;

    &:hover .vimeography-title {
      transform: translate3d(0, 0, 0);
    }

    &:hover .vimeography-title::after,
    &:hover p {
      opacity: 1;
      transform: translate3d(0, 0, 0);
    }
  }

  /* IE11 Support */
  @media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
    .vimeography-thumbnail {
      flex: 0 0 25%;
    }
  }

  .vimeography-link {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1000;
    text-indent: 200%;
    white-space: nowrap;
    font-size: 0;
    opacity: 0;
  }

  .vimeography-link-active {
  }

  .vimeography-thumbnail-img {
    cursor: pointer;
    position: relative;
    display: block;
    width: 100%;
    height: auto;
    opacity: 0.8;
  }

  figcaption {
    padding: 1.6rem 2rem 2rem;
    text-transform: uppercase;
    font-size: 1.25em;
    backface-visibility: hidden;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    text-align: left;
  }

  .vimeography-title {
    font-family: 'Montserrat', sans-serif;
    font-size: 1.2rem;
    line-height: 1.3rem;
    word-spacing: -0.15rem;
    font-weight: 300;
    margin: 0 0 1rem; /* Space to subtitle */
    padding: 0 0 0.5rem; /* Space to line */
    transition: transform 0.35s;
    transform: translate3d(0, 20px, 0);
    color: #fff;

    &:after {
      opacity: 0;
      transition: opacity 0.35s, transform 0.35s;
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      height: 3px;
      background: #fff;
      content: '';
      transform: translate3d(0, 40px, 0);
    }
  }

  .vimeography-subtitle {
    margin: 0;
    font-family: 'Open Sans', sans-serif;
    letter-spacing: 1px;
    font-size: 68.5%;
    bottom: 30px;
    line-height: 1.5;
    transform: translate3d(0, 100%, 0);
    opacity: 0;
    transition: opacity 0.35s, transform 0.35s;
    color: #fff;
  }
</style>
