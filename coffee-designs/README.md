# Roast Works — Coffee Design Archive

A bold, artsy portfolio site for coffee-related design work. Static
front end (HTML/CSS/JS) backed by two small PHP API endpoints.

```
coffee-designs/
├── index.html          # main page
├── css/style.css        # all styling
├── js/script.js         # fetches gallery data, handles the contact form
├── api/
│   ├── designs.php      # GET  — returns gallery items as JSON
│   └── contact.php      # POST — validates + (eventually) sends contact messages
├── images/               # drop real design images here (see below)
├── vercel.json           # tells Vercel to run api/*.php with the PHP runtime
├── .vercelignore
└── .gitignore
```

## How it fits together

- `index.html` loads `js/script.js`, which calls `GET /api/designs.php`
  to fetch the list of design pieces and renders them as cards. Filter
  chips are generated automatically from whatever categories exist in
  that data.
- The contact form posts JSON to `POST /api/contact.php`, which
  validates the input server-side and returns a JSON result. Actually
  sending the email is stubbed out with a clearly marked `TODO` in
  `api/contact.php` — see that file for a ready-to-uncomment example
  using a transactional email API over cURL. PHP's built-in `mail()`
  generally will **not** work on serverless platforms like Vercel.

## Adding your real designs

Open `api/designs.php` and edit the `$designs` array — each item needs
`id`, `title`, `category`, `description`, and an `image` path. Drop the
actual image files into `/images/`. The front end currently renders a
styled placeholder tile (color + icon) per category rather than an
`<img>`, so it looks intentional even before real images are wired in —
when you're ready, update `js/script.js`'s `renderGallery()` to render
`<img src="${item.image}">` instead of the icon block.

## Deploying

**1. Push to GitHub**

```bash
cd coffee-designs
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/<your-username>/<your-repo>.git
git push -u origin main
```

**2. Import into Vercel**

1. Go to [vercel.com/new](https://vercel.com/new) and import the GitHub repo.
2. Framework preset: choose **Other** (this is a static site, no build step needed).
3. Deploy. Vercel reads `vercel.json` and automatically runs
   `api/*.php` using the [vercel-php](https://github.com/vercel-community/php)
   community runtime — no extra setup required.

That's it — `/`, `/api/designs.php`, and `/api/contact.php` will all be
live on your `*.vercel.app` URL (and any custom domain you attach).

## Local development

The front end is plain static files, so you can preview it with any
static server, e.g.:

```bash
npx serve .
```

To test the PHP endpoints locally you'll need PHP installed
(`php -v` to check), then run:

```bash
vercel dev
```

which uses the same PHP runtime Vercel uses in production.
