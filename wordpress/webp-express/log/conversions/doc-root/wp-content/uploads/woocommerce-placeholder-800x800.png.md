WebP Express 0.14.22. Conversion triggered using bulk conversion, 2019-08-19 10:47:50

*WebP Convert 2.1.4*  ignited.
- PHP version: 7.3.6
- Server software: Apache/2.4.25 (Debian)

Stack converter ignited

Options:
------------
The following options have been set explicitly. Note: it is the resulting options after merging down the "jpeg" and "png" options and any converter-prefixed options.
- source: [doc-root]/wp-content/uploads/woocommerce-placeholder-800x800.png
- destination: [doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/woocommerce-placeholder-800x800.png.webp
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
- source: [doc-root]/wp-content/uploads/woocommerce-placeholder-800x800.png
- destination: [doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/woocommerce-placeholder-800x800.png.webp
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
nice [doc-root]/wp-content/plugins/webp-express/vendor/rosell-dk/webp-convert/src/Convert/Converters/Binaries/cwebp-linux-0.6.1 -metadata none -q 85 -alpha_q '80' -m 6 -low_memory '[doc-root]/wp-content/uploads/woocommerce-placeholder-800x800.png' -o '[doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/woocommerce-placeholder-800x800.png.webp.lossy.webp' 2>&1

*Output:* 
Saving file '[doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/woocommerce-placeholder-800x800.png.webp.lossy.webp'
File:      [doc-root]/wp-content/uploads/woocommerce-placeholder-800x800.png
Dimension: 800 x 800
Output:    4694 bytes Y-U-V-All-PSNR 54.65 60.11 63.07   55.97 dB
block count:  intra4: 213
              intra16: 2287  (-> 91.48%)
              skipped block: 2206 (88.24%)
bytes used:  header:             81  (1.7%)
             mode-partition:   1875  (39.9%)
 Residuals bytes  |segment 1|segment 2|segment 3|segment 4|  total
  intra4-coeffs:  |    2188 |       8 |      39 |     122 |    2357  (50.2%)
 intra16-coeffs:  |     110 |       3 |      20 |     170 |     303  (6.5%)
  chroma coeffs:  |      11 |       1 |       2 |      38 |      52  (1.1%)
    macroblocks:  |      10%|       0%|       0%|      88%|    2500
      quantizer:  |      20 |      20 |      16 |      12 |
   filter level:  |       7 |       5 |       7 |      12 |
------------------+---------+---------+---------+---------+-----------------
 segments total:  |    2309 |      12 |      61 |     330 |    2712  (57.8%)

Success
Reduction: 92% (went from 59 kb to 5 kb)

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
nice [doc-root]/wp-content/plugins/webp-express/vendor/rosell-dk/webp-convert/src/Convert/Converters/Binaries/cwebp-linux-0.6.1 -metadata none -q 85 -alpha_q '80' -near_lossless 60 -m 6 -low_memory '[doc-root]/wp-content/uploads/woocommerce-placeholder-800x800.png' -o '[doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/woocommerce-placeholder-800x800.png.webp.lossless.webp' 2>&1

*Output:* 
Saving file '[doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/woocommerce-placeholder-800x800.png.webp.lossless.webp'
File:      [doc-root]/wp-content/uploads/woocommerce-placeholder-800x800.png
Dimension: 800 x 800
Output:    26586 bytes
Lossless-ARGB compressed size: 26586 bytes
  * Header size: 1294 bytes, image data size: 25266
  * Lossless features used: PREDICTION CROSS-COLOR-TRANSFORM SUBTRACT-GREEN
  * Precision Bits: histogram=4 transform=4 cache=10

Success
Reduction: 56% (went from 59 kb to 26 kb)

Picking lossy
cwebp succeeded :)

Converted image in 869 ms, reducing file size with 92% (went from 59 kb to 5 kb)
