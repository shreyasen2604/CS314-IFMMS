# Login Page Background Customization Guide

## How to Add Your Own Background Image

### Option 1: Using a Local Image (Recommended)

1. **Place your image file** in the `public/images/` folder
   - Recommended name: `login-bg.jpg` (or `.png`, `.webp`)
   - Recommended size: 1920x1080 or higher
   - File should be optimized (under 500KB for best performance)

2. **Edit the login page** at `resources/views/auth/login.blade.php`

3. **Find this section** in the `<style>` tag:
   ```css
   .login-bg {
     /* Option 1: Use a local image - place your image in public/images/login-bg.jpg */
     /* background: linear-gradient(135deg, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.5)),
                 url('/images/login-bg.jpg'); */
   ```

4. **Uncomment Option 1** and comment out Option 2:
   ```css
   .login-bg {
     /* Option 1: Use a local image - place your image in public/images/login-bg.jpg */
     background: linear-gradient(135deg, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.5)),
                 url('/images/login-bg.jpg');
     
     /* Option 2: Use an online image (current - truck/logistics theme) */
     /* background: linear-gradient(135deg, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.5)),
                 url('https://images.unsplash.com/...'); */
   ```

5. **Replace `login-bg.jpg`** with your actual filename if different

### Option 2: Using an Online Image

1. **Find your image URL** (must be publicly accessible)

2. **Replace the URL** in Option 2:
   ```css
   background: linear-gradient(135deg, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.5)),
               url('YOUR_IMAGE_URL_HERE');
   ```

### Customizing the Overlay

The gradient overlay darkens the image for better text readability. You can adjust it:

- **Darker overlay** (better readability):
  ```css
  background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.6)),
              url('/images/login-bg.jpg');
  ```

- **Lighter overlay** (more visible image):
  ```css
  background: linear-gradient(135deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.3)),
              url('/images/login-bg.jpg');
  ```

- **Colored overlay** (brand colors):
  ```css
  background: linear-gradient(135deg, rgba(31, 41, 55, 0.8), rgba(55, 65, 81, 0.6)),
              url('/images/login-bg.jpg');
  ```

### Adjusting the Login Box Transparency

Find the `.glass-effect` class and adjust the opacity:

```css
.glass-effect {
  background: rgba(255, 255, 255, 0.95); /* 0.95 = 95% opaque */
  backdrop-filter: blur(10px);
}
```

- More transparent: `rgba(255, 255, 255, 0.85)`
- Less transparent: `rgba(255, 255, 255, 0.98)`

## Pre-selected Fleet Management Images

The login page includes several commented-out options for fleet/logistics themed images:

1. **Truck on highway** (current default)
2. **Fleet parking lot** - Uncomment line 18-19
3. **Truck interior** - Uncomment line 21-22
4. **Maintenance garage** - Uncomment line 24-25

## Image Recommendations

- **Format**: JPG for photos, PNG for graphics with transparency
- **Resolution**: Minimum 1920x1080
- **File size**: Keep under 500KB for fast loading
- **Content**: Professional fleet, logistics, or transportation themed
- **Brightness**: Medium to dark images work best with the overlay

## Testing Your Changes

1. Save your changes to `login.blade.php`
2. Clear browser cache (Ctrl+F5)
3. Visit http://127.0.0.1:8000/login
4. The new background should appear immediately

## Troubleshooting

- **Image not showing**: Check the file path and ensure the image is in `public/images/`
- **Image too bright**: Increase the overlay darkness
- **Text hard to read**: Adjust the `.glass-effect` background opacity
- **Image loads slowly**: Optimize/compress your image file

## Example Custom Configurations

### Dark Professional Look
```css
background: linear-gradient(135deg, rgba(0, 0, 0, 0.85), rgba(0, 0, 0, 0.7)),
            url('/images/login-bg.jpg');
```

### Light Modern Look
```css
background: linear-gradient(135deg, rgba(255, 255, 255, 0.7), rgba(240, 240, 240, 0.5)),
            url('/images/login-bg.jpg');
```

### Brand Color Overlay (Gray/Blue)
```css
background: linear-gradient(135deg, rgba(31, 41, 55, 0.8), rgba(59, 130, 246, 0.4)),
            url('/images/login-bg.jpg');
```