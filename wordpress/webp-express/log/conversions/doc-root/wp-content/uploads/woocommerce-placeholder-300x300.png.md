WebP Express 0.14.22. Conversion triggered using bulk conversion, 2019-08-19 10:47:47

*WebP Convert 2.1.4*  ignited.
- PHP version: 7.3.6
- Server software: Apache/2.4.25 (Debian)

Stack converter ignited

Options:
------------
The following options have been set explicitly. Note: it is the resulting options after merging down the "jpeg" and "png" options and any converter-prefixed options.
- source: [doc-root]/wp-content/uploads/woocommerce-placeholder-300x300.png
- destination: [doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/woocommerce-placeholder-300x300.png.webp
- log-call-arguments: true
- converters: (array of 9 items)

The following options have not been explicitly set, so using the following defaults:
- converter-options: (empty array)
- shuffle: false
- preferred-converters: (empty array)
- extra-converters: (empty array)

The following options were supplied and are passed on to the converters in the stack:
- alpha-quality: 80
- encoding: "auto"
- metadata: "none"
- near-lossless: 60
- quality: 85
------------


*Trying: cwebp* 

Options:
------------
The following options have been set explicitly. Note: it is the resulting options after merging down the "jpeg" and "png" options and any converter-prefixed options.
- source: [doc-root]/wp-content/uploads/woocommerce-placeholder-300x300.png
- destination: [doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/woocommerce-placeholder-300x300.png.webp
- alpha-quality: 80
- encoding: "auto"
- low-memory: true
- log-call-arguments: true
- metadata: "none"
- method: 6
- near-lossless: 60
- quality: 85
- use-nice: true
- command-line-options: ""
- try-common-system-paths: true
- try-supplied-binary-for-os: true

The following options have not been explicitly set, so using the following defaults:
- auto-filter: false
- default-quality: 85
- max-quality: 85
- preset: "none"
- size-in-percentage: null (not set)
- skip: false
- rel-path-to-precompiled-binaries: *****
------------

Encoding is set to auto - converting to both lossless and lossy and selecting the smallest file

Converting to lossy
Locating cwebp binaries
No cwebp binaries where located in common system locations
Checking if we have a supplied binary for OS: Linux... We do.
We in fact have 3
A total of 3 cwebp binaries where found
Detecting versions of the cwebp binaries found (and verifying that they can be executed in the process)
Executing: [doc-root]/wp-content/plugins/webp-express/vendor/rosell-dk/webp-convert/src/Convert/Converters/Binaries/cwebp-linux-1.0.2-shared -version
Exec failed (the cwebp binary was not found at path: [doc-root]/wp-content/plugins/webp-express/vendor/rosell-dk/webp-convert/src/Convert/Converters/Binaries/cwebp-linux-1.0.2-shared)
Executing: [doc-root]/wp-content/plugins/webp-express/vendor/rosell-dk/webp-convert/src/Convert/Converters/Binaries/cwebp-linux-1.0.2-static -version
Exec failed (the cwebp binary was not found at path: [doc-root]/wp-content/plugins/webp-express/vendor/rosell-dk/webp-convert/src/Convert/Converters/Binaries/cwebp-linux-1.0.2-static)
Executing: [doc-root]/wp-content/plugins/webp-express/vendor/rosell-dk/webp-convert/src/Convert/Converters/Binaries/cwebp-linux-0.6.1 -version. Result: version: 0.6.1
Trying executing the cwebs found until success. Starting with the ones with highest version number.
Creating command line options for version: 0.6.1
Quality: 85. 
The near-lossless option ignored for lossy
Trying to convert by executing the following command:
nice [doc-root]/wp-content/plugins/webp-express/vendor/rosell-dk/webp-convert/src/Convert/Converters/Binaries/cwebp-linux-0.6.1 -metadata none -q 85 -alpha_q '80' -m 6 -low_memory '[doc-root]/wp-content/uploads/woocommerce-placeholder-300x300.png' -o '[doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/woocommerce-placeholder-300x300.png.webp.lossy.webp' 2>&1

*Output:* 
Saving file '[doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/woocommerce-placeholder-300x300.png.webp.lossy.webp'
File:      [doc-root]/wp-content/uploads/woocommerce-placeholder-300x300.png
Dimension: 300 x 300
Output:    1122 bytes Y-U-V-All-PSNR 51.73 57.98 60.39   53.10 dB
block count:  intra4: 39
              intra16: 322  (-> 89.20%)
              skipped block: 310 (85.87%)
bytes used:  header:             36  (3.2%)
             mode-partition:    342  (30.5%)
 Residuals bytes  |segment 1|segment 2|segment 3|segment 4|  total
  intra4-coeffs:  |     604 |       6 |      14 |      12 |     636  (56.7%)
 intra16-coeffs:  |      51 |       0 |       0 |      14 |      65  (5.8%)
  chroma coeffs:  |       4 |       0 |       1 |      10 |      15  (1.3%)
    macroblocks:  |      21%|       0%|       0%|      77%|     361
      quantizer:  |      20 |      20 |      14 |      11 |
   filter level:  |       7 |       5 |       2 |       0 |
------------------+---------+---------+---------+---------+-----------------
 segments total:  |     659 |       6 |      15 |      36 |     716  (63.8%)

Success
Reduction: 90% (went from 12 kb to 1 kb)

Converting to lossless
Locating cwebp binaries
No cwebp binaries where located in common system locations
Checking if we have a supplied binary for OS: Linux... We do.
We in fact have 3
A total of 3 cwebp binaries where found
Detecting versions of the cwebp binaries found (and verifying that they can be executed in the process)
Executing: [doc-root]/wp-content/plugins/webp-express/vendor/rosell-dk/webp-convert/src/Convert/Converters/Binaries/cwebp-linux-1.0.2-shared -version
Exec failed (the cwebp binary was not found at path: [doc-root]/wp-content/plugins/webp-express/vendor/rosell-dk/webp-convert/src/Convert/Converters/Binaries/cwebp-linux-1.0.2-shared)
Executing: [doc-root]/wp-content/plugins/webp-express/vendor/rosell-dk/webp-convert/src/Convert/Converters/Binaries/cwebp-linux-1.0.2-static -version
Exec failed (the cwebp binary was not found at path: [doc-root]/wp-content/plugins/webp-express/vendor/rosell-dk/webp-convert/src/Convert/Converters/Binaries/cwebp-linux-1.0.2-static)
Executing: [doc-root]/wp-content/plugins/webp-express/vendor/rosell-dk/webp-convert/src/Convert/Converters/Binaries/cwebp-linux-0.6.1 -version. Result: version: 0.6.1
Trying executing the cwebs found until success. Starting with the ones with highest version number.
Creating command line options for version: 0.6.1
Trying to convert by executing the following command:
nice [doc-root]/wp-content/plugins/webp-express/vendor/rosell-dk/webp-convert/src/Convert/Converters/Binaries/cwebp-linux-0.6.1 -metadata none -q 85 -alpha_q '80' -near_lossless 60 -m 6 -low_memory '[doc-root]/wp-content/uploads/woocommerce-placeholder-300x300.png' -o '[doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/woocommerce-placeholder-300x300.png.webp.lossless.webp' 2>&1

*Output:* 
Saving file '[doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/woocommerce-placeholder-300x300.png.webp.lossless.webp'
File:      [doc-root]/wp-content/uploads/woocommerce-placeholder-300x300.png
Dimension: 300 x 300
Output:    5932 bytes
Lossless-ARGB compressed size: 5932 bytes
  * Header size: 335 bytes, image data size: 5571
  * Lossless features used: SUBTRACT-GREEN
  * Precision Bits: histogram=3 transform=3 cache=10

Success
Reduction: 50% (went from 12 kb to 6 kb)

Picking lossy
cwebp succeeded :)

Converted image in 201 ms, reducing file size with 90% (went from 12 kb to 1 kb)
