WebP Express 0.14.22. Conversion triggered using bulk conversion, 2019-08-19 10:47:47

*WebP Convert 2.1.4*  ignited.
- PHP version: 7.3.6
- Server software: Apache/2.4.25 (Debian)

Stack converter ignited
Destination folder does not exist. Creating folder: [doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads

Options:
------------
The following options have been set explicitly. Note: it is the resulting options after merging down the "jpeg" and "png" options and any converter-prefixed options.
- source: [doc-root]/wp-content/uploads/woocommerce-placeholder-150x150.png
- destination: [doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/woocommerce-placeholder-150x150.png.webp
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
- source: [doc-root]/wp-content/uploads/woocommerce-placeholder-150x150.png
- destination: [doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/woocommerce-placeholder-150x150.png.webp
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
nice [doc-root]/wp-content/plugins/webp-express/vendor/rosell-dk/webp-convert/src/Convert/Converters/Binaries/cwebp-linux-0.6.1 -metadata none -q 85 -alpha_q '80' -m 6 -low_memory '[doc-root]/wp-content/uploads/woocommerce-placeholder-150x150.png' -o '[doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/woocommerce-placeholder-150x150.png.webp.lossy.webp' 2>&1

*Output:* 
Saving file '[doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/woocommerce-placeholder-150x150.png.webp.lossy.webp'
File:      [doc-root]/wp-content/uploads/woocommerce-placeholder-150x150.png
Dimension: 150 x 150
Output:    422 bytes Y-U-V-All-PSNR 49.64 55.97 60.03   51.06 dB
block count:  intra4: 15
              intra16: 85  (-> 85.00%)
              skipped block: 83 (83.00%)
bytes used:  header:             29  (6.9%)
             mode-partition:    110  (26.1%)
 Residuals bytes  |segment 1|segment 2|segment 3|segment 4|  total
  intra4-coeffs:  |     228 |       0 |       0 |       0 |     228  (54.0%)
 intra16-coeffs:  |      20 |       0 |       0 |       0 |      20  (4.7%)
  chroma coeffs:  |       2 |       0 |       0 |       0 |       2  (0.5%)
    macroblocks:  |      35%|       0%|       0%|      65%|     100
      quantizer:  |      20 |      17 |      14 |       9 |
   filter level:  |       7 |       4 |       2 |       0 |
------------------+---------+---------+---------+---------+-----------------
 segments total:  |     250 |       0 |       0 |       0 |     250  (59.2%)

Success
Reduction: 90% (went from 4209 bytes to 422 bytes)

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
nice [doc-root]/wp-content/plugins/webp-express/vendor/rosell-dk/webp-convert/src/Convert/Converters/Binaries/cwebp-linux-0.6.1 -metadata none -q 85 -alpha_q '80' -near_lossless 60 -m 6 -low_memory '[doc-root]/wp-content/uploads/woocommerce-placeholder-150x150.png' -o '[doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/woocommerce-placeholder-150x150.png.webp.lossless.webp' 2>&1

*Output:* 
Saving file '[doc-root]/wp-content/webp-express/webp-images/doc-root/wp-content/uploads/woocommerce-placeholder-150x150.png.webp.lossless.webp'
File:      [doc-root]/wp-content/uploads/woocommerce-placeholder-150x150.png
Dimension: 150 x 150
Output:    1706 bytes
Lossless-ARGB compressed size: 1706 bytes
  * Header size: 98 bytes, image data size: 1582
  * Lossless features used: SUBTRACT-GREEN
  * Precision Bits: histogram=2 transform=2 cache=7

Success
Reduction: 59% (went from 4209 bytes to 1706 bytes)

Picking lossy
cwebp succeeded :)

Converted image in 1139 ms, reducing file size with 90% (went from 4209 bytes to 422 bytes)
