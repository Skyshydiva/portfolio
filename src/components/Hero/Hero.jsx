import React from "react";

import styles from "./Hero.module.css";
import { getImageUrl } from "../../utils";

export const Hero = () => {
    return (
        <section className={styles.container}>
            <div className={styles.content}>
                <h1 className={styles.title}>Hi, I'm Shyanne</h1>
                <p className={styles.description}>I'm a prospective software/web developer with 5 years of experience 
                    using HTML, CSS, Javascript, MySQL and Java. I also have 2 years 
                    of experience with Python, PHP, C#, Flutter, and React. Reach out if you'd like to 
                    learn more!
                </p>
                <a href="mailto:ShyanneCortes@gmail.com" className={styles.contactBtn}>Contact Me</a>
                </div>
                <img src={getImageUrl("hero/heroImage.png")} alt="Hero image of me" className={styles.heroImg}/>
                <div className={styles.topBlur}/>
                <div className={styles.bottomBlur}/>
        </section>
    )
};